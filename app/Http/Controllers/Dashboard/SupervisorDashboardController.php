<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\User;
use App\Models\Location\Region;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SupervisorDashboardController extends Controller
{
    public function index()
    {
        // Get extension officers (supervised by this user or all if admin created them)
        $extensionOfficers = User::where('role', UserRole::EXTENSION_OFFICER->value)
            ->active()
            ->get();

        $extensionOfficerIds = $extensionOfficers->pluck('id');

        // Stats
        $stats = [
            'total_extension_officers' => $extensionOfficers->count(),
            'total_farmers' => Farmer::whereIn('extension_officer_id', $extensionOfficerIds)->count(),
            'active_farmers' => Farmer::whereIn('extension_officer_id', $extensionOfficerIds)->active()->count(),
            'pending_approvals' => Farmer::whereIn('extension_officer_id', $extensionOfficerIds)
                ->where('status', 'pending')
                ->count(),
            'total_farms' => Farm::whereHas('farmer', function($q) use ($extensionOfficerIds) {
                $q->whereIn('extension_officer_id', $extensionOfficerIds);
            })->count(),
        ];

        // Extension officers with their farmer counts
        $officersWithStats = $extensionOfficers->map(function($officer) {
            $officer->farmers_count = Farmer::where('extension_officer_id', $officer->id)->count();
            $officer->farms_count = Farm::whereHas('farmer', function($q) use ($officer) {
                $q->where('extension_officer_id', $officer->id);
            })->count();
            return $officer;
        });

        // Recent farmers registered by extension officers
        $recentFarmers = Farmer::whereIn('extension_officer_id', $extensionOfficerIds)
            ->with(['extensionOfficer', 'village'])
            ->latest()
            ->take(5)
            ->get();

        // Pending approvals
        $pendingApprovals = Farmer::whereIn('extension_officer_id', $extensionOfficerIds)
            ->where('status', 'pending')
            ->with(['extensionOfficer', 'village'])
            ->latest()
            ->take(5)
            ->get();

        // This week's activity
        $thisWeekStats = [
            'new_farmers' => Farmer::whereIn('extension_officer_id', $extensionOfficerIds)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'new_farms' => Farm::whereHas('farmer', function($q) use ($extensionOfficerIds) {
                $q->whereIn('extension_officer_id', $extensionOfficerIds);
            })->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
        ];

        return view('dashboard.supervisor.index', compact(
            'stats',
            'officersWithStats',
            'recentFarmers',
            'pendingApprovals',
            'thisWeekStats'
        ));
    }

    public function team()
    {
        $extensionOfficers = User::where('role', UserRole::EXTENSION_OFFICER->value)
            ->active()
            ->withCount(['assignedFarmers' => function($q) {
                // Count farmers assigned to this officer
            }])
            ->get();

        // Add statistics to each officer
        $extensionOfficers = $extensionOfficers->map(function($officer) {
            $officer->farmers_count = Farmer::where('extension_officer_id', $officer->id)->count();
            $officer->farms_count = Farm::whereHas('farmer', function($q) use ($officer) {
                $q->where('extension_officer_id', $officer->id);
            })->count();
            $officer->active_farmers = Farmer::where('extension_officer_id', $officer->id)->active()->count();
            $officer->pending_farmers = Farmer::where('extension_officer_id', $officer->id)
                ->where('status', 'pending')->count();
            return $officer;
        });

        // Summary stats
        $totalFarmers = $extensionOfficers->sum('farmers_count');
        $totalFarms = $extensionOfficers->sum('farms_count');
        $avgFarmersPerOfficer = $extensionOfficers->count() > 0
            ? round($totalFarmers / $extensionOfficers->count(), 1)
            : 0;

        return view('dashboard.supervisor.team', compact(
            'extensionOfficers',
            'totalFarmers',
            'totalFarms',
            'avgFarmersPerOfficer'
        ));
    }

    public function dataReview(Request $request)
    {
        $extensionOfficerIds = User::where('role', UserRole::EXTENSION_OFFICER->value)
            ->active()
            ->pluck('id');

        // Get pending farmers for approval
        $query = Farmer::whereIn('extension_officer_id', $extensionOfficerIds)
            ->with(['extensionOfficer', 'village', 'farms']);

        // Filter by status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by extension officer
        if ($request->filled('officer_id')) {
            $query->where('extension_officer_id', $request->officer_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        $submissions = $query->latest()->paginate(15);

        // Get extension officers for filter dropdown
        $extensionOfficers = User::where('role', UserRole::EXTENSION_OFFICER->value)
            ->active()
            ->get();

        // Stats
        $stats = [
            'pending' => Farmer::whereIn('extension_officer_id', $extensionOfficerIds)
                ->where('status', 'pending')->count(),
            'approved' => Farmer::whereIn('extension_officer_id', $extensionOfficerIds)
                ->where('status', 'active')->count(),
            'rejected' => Farmer::whereIn('extension_officer_id', $extensionOfficerIds)
                ->where('status', 'rejected')->count(),
        ];

        return view('dashboard.supervisor.data-review', compact(
            'submissions',
            'extensionOfficers',
            'stats',
            'status'
        ));
    }
}
