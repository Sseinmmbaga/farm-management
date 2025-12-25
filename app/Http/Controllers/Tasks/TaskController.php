<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskAssignment;
use App\Models\User;
use App\Models\Farms\Farm;
use App\Models\Farms\Field;
use App\Models\Farms\Season;
use App\Models\Farmers\Farmer;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskType;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $query = Task::with(['farm', 'field', 'farmer', 'season', 'assignments.assignee']);

        // Apply filters
        $this->applyFilters($query, $request);

        // Apply role-based visibility
        $this->applyRoleFilters($query);

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'priority') {
            $query->orderByPriority();
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tasks = $query->paginate(20)->withQueryString();

        // Get filter options
        $taskTypes = TaskType::cases();
        $taskStatuses = TaskStatus::cases();
        $taskPriorities = TaskPriority::cases();
        $farms = $this->getAccessibleFarms();
        $seasons = Season::orderBy('start_date', 'desc')->get();

        return view('tasks.index', compact(
            'tasks',
            'taskTypes',
            'taskStatuses',
            'taskPriorities',
            'farms',
            'seasons'
        ));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Task::class);

        $farms = $this->getAccessibleFarms();
        $fields = collect();
        $farmers = $this->getAccessibleFarmers();
        $seasons = Season::orderBy('start_date', 'desc')->get();
        $taskTypes = TaskType::cases();
        $taskPriorities = TaskPriority::cases();
        $assignableUsers = $this->getAssignableUsers();

        // Pre-select values from request
        $selectedFarm = $request->has('farm_id') ? Farm::find($request->farm_id) : null;
        $selectedField = $request->has('field_id') ? Field::find($request->field_id) : null;
        $selectedFarmer = $request->has('farmer_id') ? Farmer::find($request->farmer_id) : null;

        if ($selectedFarm) {
            $fields = Field::forFarm($selectedFarm->id)->get();
        }

        return view('tasks.create', compact(
            'farms',
            'fields',
            'farmers',
            'seasons',
            'taskTypes',
            'taskPriorities',
            'assignableUsers',
            'selectedFarm',
            'selectedField',
            'selectedFarmer'
        ));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', TaskType::values()),
            'priority' => 'required|in:' . implode(',', TaskPriority::values()),
            'farm_id' => 'nullable|exists:farms,id',
            'field_id' => 'nullable|exists:fields,id',
            'farmer_id' => 'nullable|exists:farmers,id',
            'season_id' => 'nullable|exists:seasons,id',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'equipment_required' => 'nullable|array',
            'materials_required' => 'nullable|array',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Generate task code
            $validated['code'] = Task::generateCode();
            $validated['status'] = TaskStatus::PENDING;

            $task = Task::create($validated);

            // Create assignments if provided
            if (!empty($validated['assignees'])) {
                foreach ($validated['assignees'] as $userId) {
                    $task->assignTo(User::find($userId), Auth::user());
                }
            }

            DB::commit();

            return redirect()->route('tasks.show', $task)
                ->with('success', 'Task created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task->load([
            'farm.farmer',
            'field',
            'farmer',
            'season',
            'assignments.assignee',
            'assignments.assignedByUser',
            'laborRecords' => function ($query) {
                $query->latest()->limit(10);
            },
            'subtasks',
            'parentTask',
            'creator',
            'completedByUser',
        ]);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $farms = $this->getAccessibleFarms();
        $fields = $task->farm_id ? Field::forFarm($task->farm_id)->get() : collect();
        $farmers = $this->getAccessibleFarmers();
        $seasons = Season::orderBy('start_date', 'desc')->get();
        $taskTypes = TaskType::cases();
        $taskStatuses = TaskStatus::cases();
        $taskPriorities = TaskPriority::cases();
        $assignableUsers = $this->getAssignableUsers();

        return view('tasks.edit', compact(
            'task',
            'farms',
            'fields',
            'farmers',
            'seasons',
            'taskTypes',
            'taskStatuses',
            'taskPriorities',
            'assignableUsers'
        ));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:' . implode(',', TaskType::values()),
            'status' => 'required|in:' . implode(',', TaskStatus::values()),
            'priority' => 'required|in:' . implode(',', TaskPriority::values()),
            'farm_id' => 'nullable|exists:farms,id',
            'field_id' => 'nullable|exists:fields,id',
            'farmer_id' => 'nullable|exists:farmers,id',
            'season_id' => 'nullable|exists:seasons,id',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'estimated_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'equipment_required' => 'nullable|array',
            'materials_required' => 'nullable|array',
        ]);

        try {
            $task->update($validated);

            return redirect()->route('tasks.show', $task)
                ->with('success', 'Task updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        try {
            $task->delete();

            return redirect()->route('tasks.index')
                ->with('success', 'Task deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }

    /**
     * Start a task.
     */
    public function start(Task $task)
    {
        $this->authorize('update', $task);

        if (!$task->canBeStarted()) {
            return back()->with('error', 'Task cannot be started in its current state.');
        }

        if ($task->start()) {
            return back()->with('success', 'Task started successfully!');
        }

        return back()->with('error', 'Failed to start task.');
    }

    /**
     * Complete a task.
     */
    public function complete(Request $request, Task $task)
    {
        $this->authorize('complete', $task);

        $validated = $request->validate([
            'completion_notes' => 'nullable|string',
            'actual_hours' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            if (isset($validated['actual_hours'])) {
                $task->actual_hours = $validated['actual_hours'];
            }
            if (isset($validated['actual_cost'])) {
                $task->actual_cost = $validated['actual_cost'];
            }

            $task->complete($validated['completion_notes'] ?? null);

            DB::commit();

            return back()->with('success', 'Task completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete task: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a task.
     */
    public function cancel(Request $request, Task $task)
    {
        $this->authorize('cancel', $task);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if ($task->cancel($validated['cancellation_reason'])) {
            return back()->with('success', 'Task cancelled successfully!');
        }

        return back()->with('error', 'Failed to cancel task.');
    }

    /**
     * Put a task on hold.
     */
    public function hold(Task $task)
    {
        $this->authorize('update', $task);

        if ($task->putOnHold()) {
            return back()->with('success', 'Task put on hold successfully!');
        }

        return back()->with('error', 'Failed to put task on hold.');
    }

    /**
     * Resume a task.
     */
    public function resume(Task $task)
    {
        $this->authorize('update', $task);

        if ($task->resume()) {
            return back()->with('success', 'Task resumed successfully!');
        }

        return back()->with('error', 'Failed to resume task.');
    }

    /**
     * Assign users to a task.
     */
    public function assign(Request $request, Task $task)
    {
        $this->authorize('assign', $task);

        $validated = $request->validate([
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['assignees'] as $userId) {
                // Check if already assigned
                if (!$task->assignments()->forUser($userId)->exists()) {
                    $task->assignTo(User::find($userId), Auth::user());
                }
            }

            DB::commit();

            return back()->with('success', 'Task assigned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign task: ' . $e->getMessage());
        }
    }

    /**
     * Remove an assignment from a task.
     */
    public function unassign(Task $task, TaskAssignment $assignment)
    {
        $this->authorize('assign', $task);

        if ($assignment->task_id !== $task->id) {
            return back()->with('error', 'Invalid assignment.');
        }

        try {
            $assignment->delete();
            return back()->with('success', 'Assignment removed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove assignment: ' . $e->getMessage());
        }
    }

    /**
     * Display calendar view of tasks.
     */
    public function calendar(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $query = Task::with(['farm', 'field', 'assignments.assignee']);
        $this->applyRoleFilters($query);

        // Get tasks for calendar (include planned dates)
        $tasks = $query->whereNotNull('planned_start_date')
            ->orWhereNotNull('planned_end_date')
            ->get();

        $events = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->planned_start_date?->format('Y-m-d'),
                'end' => $task->planned_end_date?->format('Y-m-d'),
                'color' => $this->getStatusColor($task->status),
                'url' => route('tasks.show', $task),
            ];
        });

        return view('tasks.calendar', compact('events'));
    }

    /**
     * Display overdue tasks.
     */
    public function overdue()
    {
        $this->authorize('viewAny', Task::class);

        $query = Task::with(['farm', 'field', 'farmer', 'assignments.assignee']);
        $this->applyRoleFilters($query);

        $tasks = $query->overdue()->orderByPriority()->paginate(20);

        return view('tasks.overdue', compact('tasks'));
    }

    /**
     * Display tasks assigned to current user.
     */
    public function myTasks(Request $request)
    {
        $query = Task::with(['farm', 'field', 'farmer', 'season', 'assignments.assignee'])
            ->assignedToUser(Auth::id());

        // Apply status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderByPriority()->paginate(20)->withQueryString();

        $taskStatuses = TaskStatus::cases();

        return view('tasks.my-tasks', compact('tasks', 'taskStatuses'));
    }

    /**
     * Get fields for a farm (AJAX).
     */
    public function getFieldsForFarm(Farm $farm)
    {
        $fields = Field::forFarm($farm->id)->select('id', 'name', 'code')->get();

        return response()->json($fields);
    }

    /**
     * Apply filters to query.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        if ($request->filled('farmer_id')) {
            $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->filled('season_id')) {
            $query->where('season_id', $request->season_id);
        }

        if ($request->filled('date_from')) {
            $query->where('planned_start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('planned_end_date', '<=', $request->date_to);
        }

        if ($request->filled('assignee_id')) {
            $query->assignedToUser($request->assignee_id);
        }

        return $query;
    }

    /**
     * Apply role-based filters.
     */
    private function applyRoleFilters($query)
    {
        $user = Auth::user();

        // Admins and supervisors see all tasks
        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::PRODUCTION_MANAGER])) {
            return $query;
        }

        // Extension officers see tasks they created or for their farmers
        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id');

            $query->where(function ($q) use ($user, $assignedFarmerIds) {
                $q->where('created_by', $user->id)
                  ->orWhereIn('farmer_id', $assignedFarmerIds)
                  ->orWhereHas('assignments', function ($sq) use ($user) {
                      $sq->where('assignee_type', User::class)
                         ->where('assignee_id', $user->id);
                  });
            });

            return $query;
        }

        // Farmers see only their tasks or assigned tasks
        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            $query->where(function ($q) use ($user) {
                $q->where('farmer_id', $user->farmer->id)
                  ->orWhereHas('assignments', function ($sq) use ($user) {
                      $sq->where('assignee_type', User::class)
                         ->where('assignee_id', $user->id);
                  });
            });

            return $query;
        }

        // Default: only show tasks assigned to user
        $query->whereHas('assignments', function ($q) use ($user) {
            $q->where('assignee_type', User::class)
              ->where('assignee_id', $user->id);
        });

        return $query;
    }

    /**
     * Get accessible farms based on user role.
     */
    private function getAccessibleFarms()
    {
        $user = Auth::user();

        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::PRODUCTION_MANAGER])) {
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
     * Get accessible farmers based on user role.
     */
    private function getAccessibleFarmers()
    {
        $user = Auth::user();

        if ($user->hasAnyRole([UserRole::ADMIN, UserRole::SUPERVISOR, UserRole::PRODUCTION_MANAGER])) {
            return Farmer::active()->orderBy('first_name')->get();
        }

        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            return Farmer::where('extension_officer_id', $user->id)->active()->orderBy('first_name')->get();
        }

        if ($user->hasRole(UserRole::FARMER) && $user->farmer) {
            return collect([$user->farmer]);
        }

        return collect();
    }

    /**
     * Get assignable users.
     */
    private function getAssignableUsers()
    {
        return User::whereIn('role', [
            UserRole::EXTENSION_OFFICER->value,
            UserRole::PRODUCTION_MANAGER->value,
            UserRole::FARMER->value,
        ])->orderBy('name')->get();
    }

    /**
     * Get color for task status (for calendar).
     */
    private function getStatusColor(TaskStatus $status): string
    {
        return match($status) {
            TaskStatus::PENDING => '#ffc107',
            TaskStatus::ASSIGNED => '#17a2b8',
            TaskStatus::IN_PROGRESS => '#007bff',
            TaskStatus::ON_HOLD => '#6c757d',
            TaskStatus::COMPLETED => '#28a745',
            TaskStatus::CANCELLED => '#343a40',
            TaskStatus::OVERDUE => '#dc3545',
        };
    }
}
