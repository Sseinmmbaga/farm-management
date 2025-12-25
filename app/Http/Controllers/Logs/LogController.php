<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Enums\LogType;
use App\Models\Logs\ActivityLog;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\Farms\Season;
use App\Services\Logs\LogService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function __construct(
        protected LogService $logService
    ) {}

    public function index(Request $request)
    {
        $logs = ActivityLog::with(['farmer', 'farm', 'creator', 'season'])
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->farmer_id, fn($q, $id) => $q->forFarmer($id))
            ->when($request->farm_id, fn($q, $id) => $q->forFarm($id))
            ->when($request->season_id, fn($q, $id) => $q->forSeason($id))
            ->when($request->date_from, fn($q, $date) => $q->where('log_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->where('log_date', '<=', $date))
            ->when($request->flagged, fn($q) => $q->flagged())
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->latest('log_date')
            ->paginate(20);

        $logTypes = LogType::cases();
        $seasons = Season::orderBy('start_date', 'desc')->get();

        return view('logs.index', compact('logs', 'logTypes', 'seasons'));
    }

    public function create(Request $request)
    {
        $type = $request->type ? LogType::from($request->type) : null;
        $farmers = Farmer::active()->get();
        $farms = Farm::active()->get();
        $seasons = Season::active()->get();
        $logTypes = LogType::cases();

        return view('logs.create', compact('type', 'farmers', 'farms', 'seasons', 'logTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'log_date' => 'required|date',
            'farmer_id' => 'nullable|exists:farmers,id',
            'farm_id' => 'nullable|exists:farms,id',
            'season_id' => 'nullable|exists:seasons,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        $log = $this->logService->create($validated, $request->all());

        return redirect()
            ->route('logs.show', $log)
            ->with('success', 'Activity log created successfully.');
    }

    public function show(ActivityLog $log)
    {
        $log->load([
            'farmer', 'farm', 'season', 'creator',
            'seedingLog', 'inputLog', 'observationLog',
            'harvestLog', 'trainingLog', 'inspectionLog',
            'assets', 'quantities.unit',
        ]);

        return view('logs.show', compact('log'));
    }

    public function edit(ActivityLog $log)
    {
        $farmers = Farmer::active()->get();
        $farms = Farm::active()->get();
        $seasons = Season::active()->get();

        $log->load(['seedingLog', 'inputLog', 'observationLog', 'harvestLog', 'trainingLog', 'inspectionLog']);

        return view('logs.edit', compact('log', 'farmers', 'farms', 'seasons'));
    }

    public function update(Request $request, ActivityLog $log)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'log_date' => 'required|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $this->logService->update($log, $validated, $request->all());

        return redirect()
            ->route('logs.show', $log)
            ->with('success', 'Activity log updated successfully.');
    }

    public function destroy(ActivityLog $log)
    {
        $log->delete();

        return redirect()
            ->route('logs.index')
            ->with('success', 'Activity log deleted successfully.');
    }

    // ==================== Type-specific views ====================

    public function seeding()
    {
        $logs = ActivityLog::seeding()->with(['farmer', 'farm', 'seedingLog'])->latest('log_date')->paginate(20);
        return view('logs.seeding.index', compact('logs'));
    }

    public function inputs()
    {
        $logs = ActivityLog::inputs()->with(['farmer', 'farm', 'inputLog'])->latest('log_date')->paginate(20);
        return view('logs.inputs.index', compact('logs'));
    }

    public function observations()
    {
        $logs = ActivityLog::observations()->with(['farmer', 'farm', 'observationLog'])->latest('log_date')->paginate(20);
        return view('logs.observations.index', compact('logs'));
    }

    public function harvests()
    {
        $logs = ActivityLog::harvests()->with(['farmer', 'farm', 'harvestLog'])->latest('log_date')->paginate(20);
        return view('logs.harvests.index', compact('logs'));
    }

    // ==================== Quick Entry ====================

    public function quickEntry(Request $request)
    {
        $type = $request->type ? LogType::from($request->type) : LogType::ACTIVITY;
        $farmers = Farmer::active()->with('farms')->get();
        $seasons = Season::active()->get();

        return view('logs.quick-entry', compact('type', 'farmers', 'seasons'));
    }
}
