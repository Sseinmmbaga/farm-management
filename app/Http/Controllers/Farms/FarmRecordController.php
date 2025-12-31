<?php

namespace App\Http\Controllers\Farms;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Models\Farms\Farm;
use App\Models\Farms\FarmRecord;
use App\Models\Farms\Season;
use App\Models\Farmers\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FarmRecordController extends Controller
{
    public function __construct()
    {
        // Restrict CRUD actions for ICS inspectors (view-only access)
        $this->middleware(function ($request, $next) {
            if (auth()->user()->isIcsInspector()) {
                abort(403, 'ICS inspectors have view-only access to farm records.');
            }
            return $next($request);
        })->only(['create', 'createNew', 'createExisting', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of farm records.
     */
    public function index(Request $request)
    {
        $query = FarmRecord::with(['farm', 'farmer', 'season']);

        // Filter by record type
        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        // Filter by season
        if ($request->filled('season_id')) {
            $query->forSeason($request->season_id);
        }

        // Filter by farm
        if ($request->filled('farm_id')) {
            $query->forFarm($request->farm_id);
        }

        // Filter by farmer
        if ($request->filled('farmer_id')) {
            $query->forFarmer($request->farmer_id);
        }

        // Filter by certification status
        if ($request->filled('certification_status')) {
            $query->withCertification($request->certification_status);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Role-based filtering
        if (Auth::user()->hasRole(UserRole::FARMER)) {
            $query->forFarmer(Auth::user()->farmer->id);
        }

        if (Auth::user()->hasRole(UserRole::EXTENSION_OFFICER)) {
            $farmerIds = Farmer::where('extension_officer_id', Auth::id())->pluck('id');
            $query->whereIn('farmer_id', $farmerIds);
        }

        $records = $query->latest()->paginate(20);
        $seasons = Season::active()->orderBy('name', 'desc')->get();

        return view('farm-records.index', compact('records', 'seasons'));
    }

    /**
     * Show the form for creating a new farm record.
     */
    public function create(Request $request)
    {
        $recordType = $request->get('type', 'new'); // 'new' or 'existing'
        $farmId = $request->get('farm_id');
        $seasonId = $request->get('season_id');

        $farm = $farmId ? Farm::with('farmer')->find($farmId) : null;
        $season = $seasonId ? Season::find($seasonId) : Season::getCurrentSeason();

        // Get farms based on user role
        $farms = $this->getAccessibleFarms();
        $seasons = Season::active()->orderBy('name', 'desc')->get();

        // Check if record already exists for this farm, season, and type
        $existingRecord = null;
        if ($farm && $season) {
            $existingRecord = FarmRecord::getForFarmAndSeason($farm->id, $season->id, $recordType);
        }

        return view('farm-records.create', compact(
            'recordType',
            'farm',
            'season',
            'farms',
            'seasons',
            'existingRecord'
        ));
    }

    /**
     * Show the form for creating a new farm record (Form 2).
     */
    public function createNew(Request $request)
    {
        $request->merge(['type' => 'new']);
        return $this->create($request);
    }

    /**
     * Show the form for creating an existing farm record (Form 3).
     */
    public function createExisting(Request $request)
    {
        $request->merge(['type' => 'existing']);
        return $this->create($request);
    }

    /**
     * Store a newly created farm record in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        // Check if record already exists
        if (FarmRecord::hasRecordForSeason($validated['farm_id'], $validated['season_id'], $validated['record_type'])) {
            return back()->withInput()
                ->with('error', 'A record already exists for this farm, season, and record type.');
        }

        // Get farmer_id from farm
        $farm = Farm::findOrFail($validated['farm_id']);
        $validated['farmer_id'] = $farm->farmer_id;

        // Authorize
        $this->authorizeRecordCreation($farm->farmer_id);

        try {
            DB::beginTransaction();

            $record = FarmRecord::create($validated);

            DB::commit();

            $redirectRoute = $validated['record_type'] === 'new'
                ? 'farm-records.new.index'
                : 'farm-records.existing.index';

            return redirect()->route('farm-records.show', $record)
                ->with('success', 'Farm record created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create farm record: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified farm record.
     */
    public function show(FarmRecord $farmRecord)
    {
        $farmRecord->load(['farm', 'farmer', 'season', 'creator', 'updater']);

        return view('farm-records.show', compact('farmRecord'));
    }

    /**
     * Show the form for editing the specified farm record.
     */
    public function edit(FarmRecord $farmRecord)
    {
        $farmRecord->load(['farm.farmer', 'season']);

        $farms = $this->getAccessibleFarms();
        $seasons = Season::active()->orderBy('name', 'desc')->get();

        return view('farm-records.edit', compact('farmRecord', 'farms', 'seasons'));
    }

    /**
     * Update the specified farm record in storage.
     */
    public function update(Request $request, FarmRecord $farmRecord)
    {
        $validated = $this->validateRequest($request, $farmRecord->id);

        // Get farmer_id from farm if farm changed
        if ($validated['farm_id'] != $farmRecord->farm_id) {
            $farm = Farm::findOrFail($validated['farm_id']);
            $validated['farmer_id'] = $farm->farmer_id;
            $this->authorizeRecordCreation($farm->farmer_id);
        }

        try {
            DB::beginTransaction();

            $farmRecord->update($validated);

            DB::commit();

            return redirect()->route('farm-records.show', $farmRecord)
                ->with('success', 'Farm record updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update farm record: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified farm record from storage.
     */
    public function destroy(FarmRecord $farmRecord)
    {
        try {
            $farmRecord->delete();

            return redirect()->route('farm-records.index')
                ->with('success', 'Farm record deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete farm record: ' . $e->getMessage());
        }
    }

    /**
     * List new farm records (Form 2).
     */
    public function indexNew(Request $request)
    {
        $request->merge(['record_type' => 'new']);
        return $this->index($request);
    }

    /**
     * List existing farm records (Form 3).
     */
    public function indexExisting(Request $request)
    {
        $request->merge(['record_type' => 'existing']);
        return $this->index($request);
    }

    /**
     * Validate request data.
     */
    private function validateRequest(Request $request, ?int $excludeId = null): array
    {
        $rules = [
            'farm_id' => 'required|exists:farms,id',
            'season_id' => 'required|exists:seasons,id',
            'record_type' => 'required|in:new,existing',
            'certification_status' => 'nullable|in:C0,C1,C2,O',
            'cattle_count' => 'nullable|integer|min:0',
            'goats_sheep_count' => 'nullable|integer|min:0',
            'oxen_count' => 'nullable|integer|min:0',
            'has_input_book' => 'nullable|boolean',
            'has_pump' => 'nullable|boolean',
            'has_chemical_seed_residue' => 'nullable|boolean',
            'has_chemical_residue' => 'nullable|boolean',
            'area_size' => 'nullable|numeric|min:0',
            'land_bought' => 'nullable|numeric|min:0',
            'land_sold' => 'nullable|numeric|min:0',
            'land_borrowed' => 'nullable|numeric|min:0',
            'land_lent' => 'nullable|numeric|min:0',
            'registration_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'impact_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];

        // Unique constraint check for new records
        if (!$excludeId) {
            $rules['farm_id'] .= '|unique:farm_records,farm_id,NULL,id,season_id,' . $request->season_id . ',record_type,' . $request->record_type;
        }

        $validated = $request->validate($rules);

        // Convert checkbox values
        $validated['has_input_book'] = $request->boolean('has_input_book');
        $validated['has_pump'] = $request->boolean('has_pump');
        $validated['has_chemical_seed_residue'] = $request->boolean('has_chemical_seed_residue');
        $validated['has_chemical_residue'] = $request->boolean('has_chemical_residue');

        // Set defaults for counts
        $validated['cattle_count'] = $validated['cattle_count'] ?? 0;
        $validated['goats_sheep_count'] = $validated['goats_sheep_count'] ?? 0;
        $validated['oxen_count'] = $validated['oxen_count'] ?? 0;

        return $validated;
    }

    /**
     * Get farms accessible to current user.
     */
    private function getAccessibleFarms()
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return Farm::with('farmer')->active()->orderBy('code')->get();
        }

        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $farmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id');
            return Farm::with('farmer')->active()->whereIn('farmer_id', $farmerIds)->orderBy('code')->get();
        }

        if ($user->hasRole(UserRole::FARMER)) {
            return Farm::with('farmer')->active()->where('farmer_id', $user->farmer->id)->orderBy('code')->get();
        }

        return collect();
    }

    /**
     * Authorize record creation for the given farmer.
     */
    private function authorizeRecordCreation($farmerId)
    {
        $user = Auth::user();

        if ($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::SUPERVISOR)) {
            return true;
        }

        if ($user->hasRole(UserRole::EXTENSION_OFFICER)) {
            $assignedFarmerIds = Farmer::where('extension_officer_id', $user->id)->pluck('id')->toArray();
            if (!in_array($farmerId, $assignedFarmerIds)) {
                abort(403, 'You can only create records for farmers assigned to you.');
            }
            return true;
        }

        if ($user->hasRole(UserRole::FARMER)) {
            if ($user->farmer->id != $farmerId) {
                abort(403, 'You can only create records for yourself.');
            }
            return true;
        }

        abort(403, 'You are not authorized to create farm records.');
    }
}
