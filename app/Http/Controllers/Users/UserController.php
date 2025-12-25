<?php

namespace App\Http\Controllers\Users;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->search($request->search)
            ->when($request->role, fn($q, $role) => $q->byRole($role))
            ->when($request->status === 'active', fn($q) => $q->active())
            ->when($request->status === 'inactive', fn($q) => $q->inactive())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::active()->count(),
            'inactive' => User::inactive()->count(),
            'admins' => User::byRole(UserRole::ADMIN)->count(),
        ];

        $roles = UserRole::cases();

        return view('users.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = UserRole::cases();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->is_active,
        ]);

        UserActivity::log($user, 'created', auth()->user());

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $activities = $user->activities()
            ->with('performedBy')
            ->recentFirst()
            ->limit(20)
            ->get();

        return view('users.show', compact('user', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = UserRole::cases();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->is_active,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        UserActivity::log($user, 'updated', auth()->user());

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        UserActivity::log($user, 'deleted', auth()->user());

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        $this->authorize('toggleStatus', $user);

        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'activated' : 'deactivated';
        UserActivity::log($user, $action, auth()->user());

        return redirect()->back()
            ->with('success', 'User ' . $action . ' successfully.');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(User $user)
    {
        $this->authorize('resetPassword', $user);

        $newPassword = Str::random(12);
        $user->update(['password' => Hash::make($newPassword)]);

        UserActivity::log($user, 'password_reset', auth()->user());

        return redirect()->back()
            ->with('success', 'Password reset successfully. New password: ' . $newPassword);
    }

    /**
     * Display user activity log.
     */
    public function activity(User $user)
    {
        $this->authorize('view', $user);

        $activities = $user->activities()
            ->with('performedBy')
            ->recentFirst()
            ->paginate(20);

        return view('users.activity', compact('user', 'activities'));
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->users)->get();
        $count = 0;

        foreach ($users as $user) {
            // Skip self and admins for non-admin users
            if ($user->id === auth()->id()) {
                continue;
            }

            if (!auth()->user()->isAdmin() && $user->isAdmin()) {
                continue;
            }

            switch ($request->action) {
                case 'activate':
                    if (auth()->user()->can('toggleStatus', $user)) {
                        $user->update(['is_active' => true]);
                        UserActivity::log($user, 'activated', auth()->user());
                        $count++;
                    }
                    break;

                case 'deactivate':
                    if (auth()->user()->can('toggleStatus', $user)) {
                        $user->update(['is_active' => false]);
                        UserActivity::log($user, 'deactivated', auth()->user());
                        $count++;
                    }
                    break;

                case 'delete':
                    if (auth()->user()->can('delete', $user)) {
                        UserActivity::log($user, 'deleted', auth()->user());
                        $user->delete();
                        $count++;
                    }
                    break;
            }
        }

        $actionLabel = match($request->action) {
            'activate' => 'activated',
            'deactivate' => 'deactivated',
            'delete' => 'deleted',
        };

        return redirect()->back()
            ->with('success', "{$count} user(s) {$actionLabel} successfully.");
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->search($request->search)
            ->when($request->role, fn($q, $role) => $q->byRole($role))
            ->when($request->status === 'active', fn($q) => $q->active())
            ->when($request->status === 'inactive', fn($q) => $q->inactive())
            ->latest()
            ->get();

        $filename = 'users_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Last Login', 'Created At']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? 'N/A',
                    $user->role->label(),
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->last_login_at?->format('Y-m-d H:i') ?? 'Never',
                    $user->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
