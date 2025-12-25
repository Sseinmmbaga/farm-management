<?php

namespace App\Http\Controllers\Farmers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmers\StoreFarmerRequest;
use App\Http\Requests\Farmers\UpdateFarmerRequest;
use App\Models\Farmers\Farmer;
use App\Models\Farmers\FarmerGroup;
use App\Models\Location\Region;
use App\Models\User;
use App\Services\Farmers\FarmerService;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    public function __construct(
        protected FarmerService $farmerService
    ) {}

    public function index(Request $request)
    {
        $farmers = Farmer::with(['village', 'district', 'region', 'extensionOfficer', 'group'])
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->region_id, fn($q, $id) => $q->inRegion($id))
            ->when($request->district_id, fn($q, $id) => $q->inDistrict($id))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->certification, fn($q, $cert) => $q->where('certification_status', $cert))
            ->when($request->group_id, fn($q, $id) => $q->inGroup($id))
            ->when($request->officer_id, fn($q, $id) => $q->assignedTo($id))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(20);

        // Calculate stats for the dashboard cards
        $stats = [
            'total' => Farmer::count(),
            'active' => Farmer::where('status', 'active')->count(),
            'this_month' => Farmer::where('created_at', '>=', now()->startOfMonth())->count(),
            'with_farms' => Farmer::has('farms')->count(),
        ];

        $regions = Region::active()->get();
        $groups = FarmerGroup::active()->get();
        $officers = User::extensionOfficers()->active()->get();

        return view('farmers.index', compact('farmers', 'regions', 'groups', 'officers', 'stats'));
    }

    public function create()
    {
        $regions = Region::active()->with('districts.villages')->get();
        $groups = FarmerGroup::active()->get();
        $officers = User::extensionOfficers()->active()->get();

        return view('farmers.create', compact('regions', 'groups', 'officers'));
    }

    public function store(StoreFarmerRequest $request)
    {
        $farmer = $this->farmerService->create($request->validated());

        return redirect()
            ->route('farmers.show', $farmer)
            ->with('success', 'Farmer registered successfully.');
    }

    public function show(Farmer $farmer)
    {
        $farmer->load([
            'village', 'district', 'region',
            'extensionOfficer', 'group', 'documents',
            'farms', 'certifications', 'inspections' => fn($q) => $q->latest()->take(5),
            'activityLogs' => fn($q) => $q->latest()->take(10),
        ]);

        return view('farmers.show', compact('farmer'));
    }

    public function edit(Farmer $farmer)
    {
        $regions = Region::active()->with('districts.villages')->get();
        $groups = FarmerGroup::active()->get();
        $officers = User::extensionOfficers()->active()->get();

        return view('farmers.edit', compact('farmer', 'regions', 'groups', 'officers'));
    }

    public function update(UpdateFarmerRequest $request, Farmer $farmer)
    {
        $this->farmerService->update($farmer, $request->validated());

        return redirect()
            ->route('farmers.show', $farmer)
            ->with('success', 'Farmer updated successfully.');
    }

    public function destroy(Farmer $farmer)
    {
        $farmer->delete();

        return redirect()
            ->route('farmers.index')
            ->with('success', 'Farmer deleted successfully.');
    }

    public function farms(Farmer $farmer)
    {
        $farms = $farmer->farms()
            ->with(['seasons', 'histories'])
            ->get();

        return view('farmers.farms', compact('farmer', 'farms'));
    }

    public function activityLogs(Farmer $farmer)
    {
        $logs = $farmer->activityLogs()
            ->with(['farm', 'creator'])
            ->latest()
            ->paginate(20);

        return view('farmers.activity-logs', compact('farmer', 'logs'));
    }

    public function inspections(Farmer $farmer)
    {
        $inspections = $farmer->inspections()
            ->with(['inspector', 'checklist'])
            ->latest()
            ->paginate(20);

        return view('farmers.inspections', compact('farmer', 'inspections'));
    }

    /**
     * Display pending farmers for approval.
     */
    public function pending(Request $request)
    {
        $farmers = Farmer::with(['village', 'district', 'region', 'extensionOfficer', 'group'])
            ->pending()
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->region_id, fn($q, $id) => $q->inRegion($id))
            ->latest()
            ->paginate(20);

        $stats = [
            'pending' => Farmer::pending()->count(),
            'approved_today' => Farmer::where('status', 'active')
                ->whereDate('updated_at', today())
                ->count(),
            'rejected_today' => Farmer::where('status', 'rejected')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        $regions = Region::active()->get();

        return view('farmers.pending', compact('farmers', 'stats', 'regions'));
    }

    /**
     * Approve a pending farmer.
     */
    public function approve(Request $request, Farmer $farmer)
    {
        if ($farmer->status !== 'pending') {
            return back()->with('error', 'This farmer is not pending approval.');
        }

        $farmer->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Log the approval
        activity()
            ->performedOn($farmer)
            ->causedBy(auth()->user())
            ->withProperties(['action' => 'approved'])
            ->log('Farmer approved');

        return back()->with('success', "Farmer {$farmer->full_name} has been approved successfully.");
    }

    /**
     * Reject a pending farmer.
     */
    public function reject(Request $request, Farmer $farmer)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($farmer->status !== 'pending') {
            return back()->with('error', 'This farmer is not pending approval.');
        }

        $farmer->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        // Log the rejection
        activity()
            ->performedOn($farmer)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'rejected',
                'reason' => $request->rejection_reason,
            ])
            ->log('Farmer rejected');

        return back()->with('success', "Farmer {$farmer->full_name} has been rejected.");
    }

    /**
     * Bulk approve multiple farmers.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'farmer_ids' => 'required|array',
            'farmer_ids.*' => 'exists:farmers,id',
        ]);

        $count = Farmer::whereIn('id', $request->farmer_ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

        return back()->with('success', "{$count} farmer(s) have been approved successfully.");
    }

    /**
     * Assign farmer to an extension officer.
     */
    public function assign(Request $request, Farmer $farmer)
    {
        $request->validate([
            'extension_officer_id' => 'required|exists:users,id',
        ]);

        $officer = User::findOrFail($request->extension_officer_id);

        $farmer->update([
            'extension_officer_id' => $request->extension_officer_id,
        ]);

        // Log the assignment
        activity()
            ->performedOn($farmer)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'assigned',
                'officer_id' => $officer->id,
                'officer_name' => $officer->name,
            ])
            ->log("Farmer assigned to {$officer->name}");

        return back()->with('success', "Farmer {$farmer->full_name} has been assigned to {$officer->name}.");
    }

    /**
     * Bulk assign farmers to an extension officer.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'farmer_ids' => 'required|array',
            'farmer_ids.*' => 'exists:farmers,id',
            'extension_officer_id' => 'required|exists:users,id',
        ]);

        $officer = User::findOrFail($request->extension_officer_id);

        $count = Farmer::whereIn('id', $request->farmer_ids)
            ->update([
                'extension_officer_id' => $request->extension_officer_id,
            ]);

        return back()->with('success', "{$count} farmer(s) have been assigned to {$officer->name}.");
    }

    /**
     * Show assignment management page.
     */
    public function assignments(Request $request)
    {
        $officers = User::extensionOfficers()
            ->active()
            ->withCount('assignedFarmers')
            ->get();

        $unassignedFarmers = Farmer::with(['village', 'district', 'region', 'group'])
            ->whereNull('extension_officer_id')
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->region_id, fn($q, $id) => $q->inRegion($id))
            ->active()
            ->latest()
            ->paginate(20);

        $stats = [
            'total_officers' => User::extensionOfficers()->active()->count(),
            'unassigned_farmers' => Farmer::whereNull('extension_officer_id')->active()->count(),
            'assigned_farmers' => Farmer::whereNotNull('extension_officer_id')->active()->count(),
        ];

        $regions = Region::active()->get();

        return view('farmers.assignments', compact('officers', 'unassignedFarmers', 'stats', 'regions'));
    }

    /**
     * Export farmers to CSV.
     */
    public function export(Request $request)
    {
        $farmers = Farmer::with(['village', 'district', 'region', 'extensionOfficer', 'group'])
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->region_id, fn($q, $id) => $q->inRegion($id))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->certification, fn($q, $cert) => $q->where('certification_status', $cert))
            ->latest()
            ->get();

        $filename = 'farmers_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($farmers) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Registration Number',
                'First Name',
                'Middle Name',
                'Last Name',
                'Gender',
                'Date of Birth',
                'National ID',
                'Phone',
                'Email',
                'Region',
                'District',
                'Village',
                'Subvillage',
                'Status',
                'Certification Status',
                'Farmer Group',
                'Extension Officer',
                'Total Farms',
                'Total Land Size (Ha)',
                'Household Size',
                'Registration Date',
            ]);

            foreach ($farmers as $farmer) {
                fputcsv($handle, [
                    $farmer->registration_number,
                    $farmer->first_name,
                    $farmer->middle_name ?? '',
                    $farmer->last_name,
                    $farmer->gender ?? 'N/A',
                    $farmer->date_of_birth?->format('Y-m-d') ?? 'N/A',
                    $farmer->national_id ?? 'N/A',
                    $farmer->phone ?? 'N/A',
                    $farmer->email ?? 'N/A',
                    $farmer->region?->name ?? 'N/A',
                    $farmer->district?->name ?? 'N/A',
                    $farmer->village?->name ?? 'N/A',
                    $farmer->subvillage ?? 'N/A',
                    $farmer->status,
                    $farmer->certification_status ?? 'N/A',
                    $farmer->group?->name ?? 'N/A',
                    $farmer->extensionOfficer?->name ?? 'Unassigned',
                    $farmer->farms->count(),
                    $farmer->total_land_size ?? 0,
                    $farmer->household_size ?? 'N/A',
                    $farmer->created_at->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
