<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Tasks\Labor;
use App\Models\Tasks\Task;
use App\Models\User;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use App\Models\Farmers\Farmer;
use App\Enums\LaborType;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaborController extends Controller
{
    /**
     * Display a listing of labor records.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Labor::class);

        $query = Labor::with(['task', 'farm', 'field', 'worker', 'verifiedByUser']);

        // Apply filters
        $this->applyFilters($query, $request);

        // Apply role-based visibility
        $this->applyRoleFilters($query);

        // Sorting
        $sortBy = $request->get('sort_by', 'work_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $laborRecords = $query->paginate(20)->withQueryString();

        // Get filter options
        $laborTypes = LaborType::cases();
        $farms = $this->getAccessibleFarms();
        $tasks = $this->getAccessibleTasks();

        // Summary statistics
        $stats = $this->getStatistics($request);

        return view('labor.index', compact(
            'laborRecords',
            'laborTypes',
            'farms',
            'tasks',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new labor record.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Labor::class);

        $laborTypes = LaborType::cases();
        $farms = $this->getAccessibleFarms();
        $fields = collect();
        $tasks = $this->getAccessibleTasks();
        $workers = $this->getAvailableWorkers();

        // Pre-select values from request
        $selectedTask = $request->has('task_id') ? Task::find($request->task_id) : null;
        $selectedFarm = $request->has('farm_id') ? Farm::find($request->farm_id) : null;
        $selectedField = $request->has('field_id') ? Field::find($request->field_id) : null;

        if ($selectedFarm) {
            $fields = Field::forFarm($selectedFarm->id)->get();
        } elseif ($selectedTask && $selectedTask->farm_id) {
            $selectedFarm = $selectedTask->farm;
            $fields = Field::forFarm($selectedTask->farm_id)->get();
            $selectedField = $selectedTask->field;
        }

        return view('labor.create', compact(
            'laborTypes',
            'farms',
            'fields',
            'tasks',
            'workers',
            'selectedTask',
            'selectedFarm',
            'selectedField'
        ));
    }

    /**
     * Store a newly created labor record in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Labor::class);

        $validated = $request->validate([
            'task_id' => 'nullable|exists:tasks,id',
            'farm_id' => 'nullable|exists:farms,id',
            'field_id' => 'nullable|exists:fields,id',
            'worker_type' => 'nullable|string',
            'worker_id' => 'nullable|integer',
            'worker_name' => 'nullable|string|max:255',
            'labor_type' => 'required|in:' . implode(',', LaborType::values()),
            'work_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'hours_worked' => 'required|numeric|min:0.25|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:12',
            'break_minutes' => 'nullable|integer|min:0|max:480',
            'hourly_rate' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'work_description' => 'nullable|string',
            'notes' => 'nullable|string',
            'weather_conditions' => 'nullable|string|max:100',
        ]);

        // Either worker_name or worker_type/worker_id must be provided
        if (empty($validated['worker_name']) && (empty($validated['worker_type']) || empty($validated['worker_id']))) {
            return back()->withInput()
                ->withErrors(['worker_name' => 'Please provide either a worker name or select a worker.']);
        }

        try {
            DB::beginTransaction();

            // Generate code
            $validated['code'] = Labor::generateCode();
            $validated['payment_status'] = 'pending';

            // Calculate total cost
            $regularCost = $validated['hours_worked'] * ($validated['hourly_rate'] ?? 0);
            $overtimeCost = ($validated['overtime_hours'] ?? 0) * ($validated['overtime_rate'] ?? ($validated['hourly_rate'] ?? 0) * 1.5);
            $validated['total_cost'] = $regularCost + $overtimeCost;

            // If task is provided, auto-fill farm/field from task
            if (!empty($validated['task_id'])) {
                $task = Task::find($validated['task_id']);
                if ($task) {
                    $validated['farm_id'] = $validated['farm_id'] ?? $task->farm_id;
                    $validated['field_id'] = $validated['field_id'] ?? $task->field_id;
                }
            }

            $labor = Labor::create($validated);

            DB::commit();

            return redirect()->route('labor.show', $labor)
                ->with('success', 'Labor record created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create labor record: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified labor record.
     */
    public function show(Labor $labor)
    {
        $this->authorize('view', $labor);

        $labor->load(['task', 'farm.farmer', 'field', 'worker', 'paidByUser', 'verifiedByUser', 'creator']);

        return view('labor.show', compact('labor'));
    }

    /**
     * Show the form for editing the specified labor record.
     */
    public function edit(Labor $labor)
    {
        $this->authorize('update', $labor);

        $laborTypes = LaborType::cases();
        $farms = $this->getAccessibleFarms();
        $fields = $labor->farm_id ? Field::forFarm($labor->farm_id)->get() : collect();
        $tasks = $this->getAccessibleTasks();
        $workers = $this->getAvailableWorkers();

        return view('labor.edit', compact(
            'labor',
            'laborTypes',
            'farms',
            'fields',
            'tasks',
            'workers'
        ));
    }

    /**
     * Update the specified labor record in storage.
     */
    public function update(Request $request, Labor $labor)
    {
        $this->authorize('update', $labor);

        $validated = $request->validate([
            'task_id' => 'nullable|exists:tasks,id',
            'farm_id' => 'nullable|exists:farms,id',
            'field_id' => 'nullable|exists:fields,id',
            'worker_type' => 'nullable|string',
            'worker_id' => 'nullable|integer',
            'worker_name' => 'nullable|string|max:255',
            'labor_type' => 'required|in:' . implode(',', LaborType::values()),
            'work_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'hours_worked' => 'required|numeric|min:0.25|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:12',
            'break_minutes' => 'nullable|integer|min:0|max:480',
            'hourly_rate' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'work_description' => 'nullable|string',
            'notes' => 'nullable|string',
            'weather_conditions' => 'nullable|string|max:100',
        ]);

        try {
            // Recalculate total cost
            $regularCost = $validated['hours_worked'] * ($validated['hourly_rate'] ?? 0);
            $overtimeCost = ($validated['overtime_hours'] ?? 0) * ($validated['overtime_rate'] ?? ($validated['hourly_rate'] ?? 0) * 1.5);
            $validated['total_cost'] = $regularCost + $overtimeCost;

            $labor->update($validated);

            return redirect()->route('labor.show', $labor)
                ->with('success', 'Labor record updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update labor record: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified labor record from storage.
     */
    public function destroy(Labor $labor)
    {
        $this->authorize('delete', $labor);

        try {
            $labor->delete();

            return redirect()->route('labor.index')
                ->with('success', 'Labor record deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete labor record: ' . $e->getMessage());
        }
    }

    /**
     * Verify a labor record.
     */
    public function verify(Labor $labor)
    {
        $this->authorize('verify', $labor);

        if ($labor->verify()) {
            return back()->with('success', 'Labor record verified successfully!');
        }

        return back()->with('error', 'Failed to verify labor record.');
    }

    /**
     * Approve a labor record for payment.
     */
    public function approve(Labor $labor)
    {
        $this->authorize('approve', $labor);

        if ($labor->approve()) {
            return back()->with('success', 'Labor record approved for payment!');
        }

        return back()->with('error', 'Failed to approve labor record.');
    }

    /**
     * Mark a labor record as paid.
     */
    public function markAsPaid(Request $request, Labor $labor)
    {
        $this->authorize('markAsPaid', $labor);

        $validated = $request->validate([
            'payment_reference' => 'nullable|string|max:100',
        ]);

        if ($labor->markAsPaid($validated['payment_reference'] ?? null)) {
            return back()->with('success', 'Labor record marked as paid!');
        }

        return back()->with('error', 'Failed to mark labor record as paid.');
    }

    /**
     * Bulk approve labor records.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'labor_ids' => 'required|array|min:1',
            'labor_ids.*' => 'exists:labor_records,id',
        ]);

        $approved = 0;
        foreach ($validated['labor_ids'] as $id) {
            $labor = Labor::find($id);
            if ($labor && $this->authorize('approve', $labor) && $labor->approve()) {
                $approved++;
            }
        }

        return back()->with('success', "{$approved} labor record(s) approved successfully!");
    }

    /**
     * Bulk mark as paid.
     */
    public function bulkPay(Request $request)
    {
        $validated = $request->validate([
            'labor_ids' => 'required|array|min:1',
            'labor_ids.*' => 'exists:labor_records,id',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $paid = 0;
        foreach ($validated['labor_ids'] as $id) {
            $labor = Labor::find($id);
            if ($labor && $this->authorize('markAsPaid', $labor) && $labor->markAsPaid($validated['payment_reference'] ?? null)) {
                $paid++;
            }
        }

        return back()->with('success', "{$paid} labor record(s) marked as paid!");
    }

    /**
     * Display labor summary for a task.
     */
    public function taskSummary(Task $task)
    {
        $this->authorize('view', $task);

        $laborRecords = Labor::forTask($task->id)
            ->with(['worker'])
            ->orderBy('work_date', 'desc')
            ->get();

        $summary = [
            'total_hours' => $laborRecords->sum('hours_worked'),
            'overtime_hours' => $laborRecords->sum('overtime_hours'),
            'total_cost' => $laborRecords->sum('total_cost'),
            'pending_cost' => $laborRecords->where('payment_status', 'pending')->sum('total_cost'),
            'paid_cost' => $laborRecords->where('payment_status', 'paid')->sum('total_cost'),
            'records_count' => $laborRecords->count(),
            'workers_count' => $laborRecords->unique('worker_name')->count(),
        ];

        return view('labor.task-summary', compact('task', 'laborRecords', 'summary'));
    }

    /**
     * Display labor report.
     */
    public function report(Request $request)
    {
        $this->authorize('export', Labor::class);

        $query = Labor::with(['task', 'farm', 'field', 'worker']);
        $this->applyFilters($query, $request);
        $this->applyRoleFilters($query);

        $laborRecords = $query->orderBy('work_date', 'desc')->get();

        // Group by different dimensions for report
        $byWorker = $laborRecords->groupBy('worker_display_name')->map(function ($records) {
            return [
                'hours' => $records->sum('hours_worked'),
                'cost' => $records->sum('total_cost'),
                'count' => $records->count(),
            ];
        });

        $byType = $laborRecords->groupBy('labor_type')->map(function ($records) {
            return [
                'hours' => $records->sum('hours_worked'),
                'cost' => $records->sum('total_cost'),
                'count' => $records->count(),
            ];
        });

        $byFarm = $laborRecords->groupBy(fn($r) => $r->farm?->name ?? 'Unassigned')->map(function ($records) {
            return [
                'hours' => $records->sum('hours_worked'),
                'cost' => $records->sum('total_cost'),
                'count' => $records->count(),
            ];
        });

        $summary = [
            'total_hours' => $laborRecords->sum('hours_worked'),
            'total_cost' => $laborRecords->sum('total_cost'),
            'total_records' => $laborRecords->count(),
            'pending_payment' => $laborRecords->whereIn('payment_status', ['pending', 'approved'])->sum('total_cost'),
        ];

        return view('labor.report', compact('laborRecords', 'byWorker', 'byType', 'byFarm', 'summary'));
    }

    /**
     * Export labor records.
     */
    public function export(Request $request)
    {
        $this->authorize('export', Labor::class);

        $query = Labor::with(['task', 'farm', 'field', 'worker']);
        $this->applyFilters($query, $request);
        $this->applyRoleFilters($query);

        $laborRecords = $query->orderBy('work_date', 'desc')->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="labor-records-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($laborRecords) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Code',
                'Work Date',
                'Worker',
                'Labor Type',
                'Farm',
                'Field',
                'Task',
                'Hours Worked',
                'Overtime Hours',
                'Hourly Rate',
                'Total Cost',
                'Payment Status',
                'Verified',
            ]);

            foreach ($laborRecords as $labor) {
                fputcsv($file, [
                    $labor->code,
                    $labor->work_date->format('Y-m-d'),
                    $labor->worker_display_name,
                    $labor->labor_type->label(),
                    $labor->farm?->name ?? '',
                    $labor->field?->name ?? '',
                    $labor->task?->title ?? '',
                    $labor->hours_worked,
                    $labor->overtime_hours ?? 0,
                    $labor->hourly_rate ?? 0,
                    $labor->total_cost ?? 0,
                    $labor->payment_status,
                    $labor->is_verified ? 'Yes' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Apply filters to query.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('labor_type')) {
            $query->where('labor_type', $request->labor_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        if ($request->filled('date_from')) {
            $query->where('work_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('work_date', '<=', $request->date_to);
        }

        if ($request->boolean('verified_only')) {
            $query->verified();
        }

        if ($request->boolean('unverified_only')) {
            $query->unverified();
        }

        return $query;
    }

    /**
     * Apply role-based filters.
     */
    private function applyRoleFilters($query)
    {
        $user = Auth::user();

        // Admins, supervisors, production managers, accountants see all
        if ($user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::PRODUCTION_MANAGER,
            UserRole::ACCOUNTANT
        ])) {
            return $query;
        }

        // Extension officers see labor for their farmers
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id');
            $farmIds = Farm::whereIn('farmer_id', $assignedFarmerIds)->pluck('id');

            $query->where(function ($q) use ($user, $farmIds) {
                $q->where('created_by', $user->id)
                  ->orWhereIn('farm_id', $farmIds)
                  ->orWhere(function ($sq) use ($user) {
                      $sq->where('worker_type', User::class)
                         ->where('worker_id', $user->id);
                  });
            });

            return $query;
        }

        // Farmers see only their records
        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            $farmIds = Farm::where('farmer_id', $user->farmer->id)->pluck('id');

            $query->where(function ($q) use ($user, $farmIds) {
                $q->whereIn('farm_id', $farmIds)
                  ->orWhere('created_by', $user->id)
                  ->orWhere(function ($sq) use ($user) {
                      $sq->where('worker_type', User::class)
                         ->where('worker_id', $user->id);
                  })
                  ->orWhere(function ($sq) use ($user) {
                      $sq->where('worker_type', Farmer::class)
                         ->where('worker_id', $user->farmer->id);
                  });
            });

            return $query;
        }

        // Default: only show user's own records
        $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere(function ($sq) use ($user) {
                  $sq->where('worker_type', User::class)
                     ->where('worker_id', $user->id);
              });
        });

        return $query;
    }

    /**
     * Get accessible farms based on user role.
     */
    private function getAccessibleFarms()
    {
        $user = Auth::user();

        if ($user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::PRODUCTION_MANAGER,
            UserRole::ACCOUNTANT
        ])) {
            return Farm::with('farmer')->orderBy('name')->get();
        }

        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $farmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id');
            return Farm::whereIn('farmer_id', $farmerIds)->with('farmer')->orderBy('name')->get();
        }

        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            return Farm::where('farmer_id', $user->farmer->id)->with('farmer')->orderBy('name')->get();
        }

        return collect();
    }

    /**
     * Get accessible tasks based on user role.
     */
    private function getAccessibleTasks()
    {
        $user = Auth::user();

        $query = Task::active()->orderBy('title');

        if ($user->hasAnyRole([
            UserRole::ADMIN,
            UserRole::SUPERVISOR,
            UserRole::PRODUCTION_MANAGER
        ])) {
            return $query->get();
        }

        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id');
            return $query->where(function ($q) use ($user, $assignedFarmerIds) {
                $q->where('created_by', $user->id)
                  ->orWhereIn('farmer_id', $assignedFarmerIds);
            })->get();
        }

        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            return $query->where('farmer_id', $user->farmer->id)->get();
        }

        return collect();
    }

    /**
     * Get available workers.
     */
    private function getAvailableWorkers()
    {
        $user = Auth::user();

        // Get users who can be workers
        $users = User::whereIn('role', [
            UserRole::EXTENSION_OFFICER->value,
            UserRole::FARMER->value,
        ])->orderBy('name')->get();

        // Get farmers
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $farmers = Farmer::where('extension_officer_id', $user->id)->active()->get();
        } elseif ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::PRODUCTION_MANAGER])) {
            $farmers = Farmer::active()->get();
        } elseif ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            $farmers = collect([$user->farmer]);
        } else {
            $farmers = collect();
        }

        return [
            'users' => $users,
            'farmers' => $farmers,
        ];
    }

    /**
     * Get statistics for labor records.
     */
    private function getStatistics(Request $request)
    {
        $query = Labor::query();
        $this->applyRoleFilters($query);

        // Apply date filter for stats (default to this month)
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->endOfMonth()->toDateString());

        $query->whereBetween('work_date', [$dateFrom, $dateTo]);

        return [
            'total_hours' => $query->sum('hours_worked'),
            'total_cost' => $query->sum('total_cost'),
            'pending_payment' => (clone $query)->whereIn('payment_status', ['pending', 'approved'])->sum('total_cost'),
            'paid' => (clone $query)->where('payment_status', 'paid')->sum('total_cost'),
            'records_count' => $query->count(),
        ];
    }
}
