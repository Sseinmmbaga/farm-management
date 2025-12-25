<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Enums\AssetType;
use App\Enums\AssetStatus;
use App\Models\Assets\Asset;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Services\Assets\AssetService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService
    ) {}

    public function index(Request $request)
    {
        $assets = Asset::with(['farmer', 'farm', 'village'])
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->farmer_id, fn($q, $id) => $q->forFarmer($id))
            ->when($request->farm_id, fn($q, $id) => $q->forFarm($id))
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->roots()
            ->latest()
            ->paginate(20);

        $assetTypes = AssetType::cases();
        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);

        return view('assets.index', compact('assets', 'assetTypes', 'farmers'));
    }

    public function create(Request $request)
    {
        $type = $request->type ? AssetType::from($request->type) : null;
        $farmers = Farmer::active()->get();
        $farms = Farm::active()->get();
        $assetTypes = AssetType::cases();

        return view('assets.create', compact('type', 'farmers', 'farms', 'assetTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'farmer_id' => 'nullable|exists:farmers,id',
            'farm_id' => 'nullable|exists:farms,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'acquired_date' => 'nullable|date',
            // Type-specific fields handled by service
        ]);

        $asset = $this->assetService->create($validated, $request->all());

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'farmer', 'farm', 'village', 'district', 'region',
            'parent', 'children',
            'landAsset', 'cropAsset', 'equipmentAsset', 'materialAsset', 'groupAsset',
            'activityLogs' => fn($q) => $q->latest()->take(10),
            'quantities.unit',
        ]);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $farmers = Farmer::active()->get();
        $farms = Farm::active()->get();
        $assetTypes = AssetType::cases();

        $asset->load(['landAsset', 'cropAsset', 'equipmentAsset', 'materialAsset', 'groupAsset']);

        return view('assets.edit', compact('asset', 'farmers', 'farms', 'assetTypes'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'farmer_id' => 'nullable|exists:farmers,id',
            'farm_id' => 'nullable|exists:farms,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $this->assetService->update($asset, $validated, $request->all());

        return redirect()
            ->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    // ==================== Type-specific views ====================

    public function land()
    {
        $assets = Asset::land()->with(['farmer', 'farm', 'landAsset'])->paginate(20);
        return view('assets.land.index', compact('assets'));
    }

    public function crops()
    {
        $assets = Asset::crops()->with(['farmer', 'farm', 'cropAsset.season'])->paginate(20);
        return view('assets.crops.index', compact('assets'));
    }

    public function equipment()
    {
        $assets = Asset::equipment()->with(['farmer', 'equipmentAsset'])->paginate(20);
        return view('assets.equipment.index', compact('assets'));
    }

    public function materials()
    {
        $assets = Asset::materials()->with(['materialAsset'])->paginate(20);
        return view('assets.materials.index', compact('assets'));
    }

    public function groups()
    {
        $assets = Asset::groups()->with(['groupAsset'])->paginate(20);
        return view('assets.groups.index', compact('assets'));
    }

    // ==================== Map view ====================

    public function map(Request $request)
    {
        $assets = Asset::with(['farmer', 'farm'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->get()
            ->map(fn($asset) => $asset->toGeoJson());

        return view('assets.map', compact('assets'));
    }
}
