<?php

namespace App\Http\Controllers\ICS;

use App\Http\Controllers\Controller;
use App\Models\ICS\ComplianceStandard;
use App\Models\ICS\FarmerCertification;
use App\Models\Farmers\Farmer;
use App\Models\ICS\Inspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplianceController extends Controller
{
    // ==================== COMPLIANCE STANDARDS CRUD ====================

    /**
     * Display a listing of compliance standards.
     */
    public function index()
    {
        $complianceStandards = ComplianceStandard::withCount(['checklists', 'certifications'])
            ->orderBy('name')
            ->paginate(20);

        $stats = [
            'total' => ComplianceStandard::count(),
            'active' => ComplianceStandard::where('is_active', true)->count(),
            'mandatory' => ComplianceStandard::where('is_mandatory', true)->count(),
            'categories' => ComplianceStandard::distinct('category')->count('category'),
        ];

        return view('ics.compliance-standards.index', compact('complianceStandards', 'stats'));
    }

    /**
     * Show the form for creating a new compliance standard.
     */
    public function create()
    {
        return view('ics.compliance-standards.create');
    }

    /**
     * Store a newly created compliance standard in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:compliance_standards,code',
            'description' => 'nullable|string',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'status' => 'required|in:draft,active,inactive',
            'requirements' => 'nullable|string',
        ]);

        $standard = ComplianceStandard::create($validated);

        return redirect()->route('compliance-standards.show', $standard)
            ->with('success', 'Compliance standard created successfully!');
    }

    /**
     * Display the specified compliance standard.
     */
    public function show(ComplianceStandard $complianceStandard)
    {
        $complianceStandard->load(['checklists', 'certifications.farmer']);
        return view('ics.compliance-standards.show', compact('complianceStandard'));
    }

    /**
     * Show the form for editing the specified compliance standard.
     */
    public function edit(ComplianceStandard $complianceStandard)
    {
        return view('ics.compliance-standards.edit', compact('complianceStandard'));
    }

    /**
     * Update the specified compliance standard in storage.
     */
    public function update(Request $request, ComplianceStandard $complianceStandard)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:compliance_standards,code,' . $complianceStandard->id,
            'description' => 'nullable|string',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'status' => 'required|in:draft,active,inactive',
            'requirements' => 'nullable|string',
        ]);

        $complianceStandard->update($validated);

        return redirect()->route('compliance-standards.show', $complianceStandard)
            ->with('success', 'Compliance standard updated successfully!');
    }

    /**
     * Remove the specified compliance standard from storage.
     */
    public function destroy(ComplianceStandard $complianceStandard)
    {
        // Check if standard is in use
        if ($complianceStandard->checklists()->exists() || $complianceStandard->certifications()->exists()) {
            return back()->with('error', 'Cannot delete standard because it is associated with checklists or certifications.');
        }

        $complianceStandard->delete();

        return redirect()->route('compliance-standards.index')
            ->with('success', 'Compliance standard deleted successfully!');
    }

    // ==================== FARMER CERTIFICATIONS ====================

    /**
     * Display all farmer certifications.
     */
    public function certifications()
    {
        $query = FarmerCertification::with(['farmer', 'lastInspection']);

        // Filter by status
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }

        // Filter by farmer name
        if (request()->has('farmer')) {
            $query->whereHas('farmer', function ($q) {
                $q->where('first_name', 'like', '%' . request('farmer') . '%')
                  ->orWhere('last_name', 'like', '%' . request('farmer') . '%');
            });
        }

        // Filter by expiry
        if (request('expiry_date') == 'expiring_soon') {
            $query->where('expiry_date', '>', now())
                  ->where('expiry_date', '<=', now()->addDays(30));
        } elseif (request('expiry_date') == 'expired') {
            $query->where('expiry_date', '<', now());
        } elseif (request('expiry_date') == 'valid') {
            $query->where('expiry_date', '>', now());
        }

        $certifications = $query->orderBy('certificate_number')->paginate(20);

        // Calculate stats
        $stats = [
            'total' => FarmerCertification::count(),
            'certified' => FarmerCertification::where('status', 'certified')->count(),
            'in_conversion' => FarmerCertification::where('status', 'in-conversion')->count(),
            'expiring_soon' => FarmerCertification::where('status', 'certified')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<=', now()->addDays(30))
                ->count(),
            'expired' => FarmerCertification::where('expiry_date', '<', now())->count(),
            'suspended' => FarmerCertification::where('status', 'suspended')->count(),
        ];

        return view('ics.certifications.index', compact('certifications', 'stats'));
    }

    /**
     * Display certifications for a specific farmer.
     */
    public function farmerCertifications(Farmer $farmer)
    {
        $certifications = FarmerCertification::where('farmer_id', $farmer->id)
            ->with(['lastInspection', 'approvedBy'])
            ->orderBy('certification_date', 'desc')
            ->get();

        $inspections = Inspection::where('farmer_id', $farmer->id)
            ->with(['checklist', 'inspector'])
            ->latest()
            ->take(10)
            ->get();

        // Calculate stats
        $stats = [
            'total' => $certifications->count(),
            'active' => $certifications->where('status', 'certified')->where('expiry_date', '>', now())->count(),
            'in_conversion' => $certifications->where('status', 'in-conversion')->count(),
            'expiring_soon' => $certifications->where('status', 'certified')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<=', now()->addDays(30))
                ->count(),
        ];

        return view('ics.certifications.farmer', compact('farmer', 'certifications', 'inspections', 'stats'));
    }

    /**
     * Store a new farmer certification.
     */
    public function storeCertification(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'certificate_number' => 'required|string|max:100|unique:farmer_certifications,certificate_number',
            'compliance_standard_id' => 'nullable|exists:compliance_standards,id',
            'status' => 'required|in:pending,in-conversion,certified,suspended,revoked',
            'certification_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:certification_date',
            'notes' => 'nullable|string',
        ]);

        $validated['approved_by'] = Auth::id();

        try {
            DB::beginTransaction();

            $certification = FarmerCertification::create($validated);

            DB::commit();

            return redirect()->route('certifications.farmer', $validated['farmer_id'])
                ->with('success', 'Certification added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create certification: ' . $e->getMessage());
        }
    }

    /**
     * Update certification status for a farmer.
     */
    public function updateCertificationStatus(Request $request, Farmer $farmer)
    {
        $validated = $request->validate([
            'certification_id' => 'required|exists:farmer_certifications,id',
            'status' => 'required|in:certified,in-conversion,pending,suspended,revoked,expired',
            'notes' => 'nullable|string',
        ]);

        $certification = FarmerCertification::findOrFail($validated['certification_id']);

        // Ensure certification belongs to this farmer
        if ($certification->farmer_id != $farmer->id) {
            return back()->with('error', 'Invalid certification for this farmer.');
        }

        $certification->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $certification->notes,
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Certification status updated successfully!');
    }

    /**
     * Bulk update certification status.
     */
    public function bulkUpdateCertificationStatus(Request $request)
    {
        $validated = $request->validate([
            'certification_ids' => 'required|string',
            'status' => 'required|in:certified,in-conversion,pending,suspended,revoked,expired',
            'notes' => 'nullable|string',
        ]);

        $ids = array_filter(explode(',', $validated['certification_ids']));

        if (empty($ids)) {
            return back()->with('error', 'No certifications selected.');
        }

        FarmerCertification::whereIn('id', $ids)->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', count($ids) . ' certification(s) updated successfully!');
    }

    // ==================== EXPORT REPORTS ====================

    /**
     * Display export reports dashboard.
     */
    public function exportIndex()
    {
        return view('ics.export.index');
    }

    /**
     * Export certifications data.
     */
    public function exportCertifications(Request $request)
    {
        // Placeholder for export logic
        $format = $request->get('format', 'pdf');
        
        // In a real application, you would generate PDF/Excel/CSV here
        return response()->json([
            'message' => 'Export feature is under development.',
            'format' => $format,
            'data' => []
        ]);
    }

    /**
     * Generate bulk export.
     */
    public function bulkExport(Request $request)
    {
        // Placeholder for bulk export logic
        return response()->json([
            'message' => 'Bulk export feature is under development.',
            'requested_data' => $request->get('include', []),
            'format' => $request->get('export_format', 'zip')
        ]);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get compliance statistics for dashboard.
     */
    public function getComplianceStats()
    {
        // This would aggregate data from inspections, findings, and corrective actions
        $totalInspections = Inspection::count();
        $completedInspections = Inspection::where('status', 'completed')->count();
        $openFindings = \App\Models\ICS\InspectionFinding::where('status', 'open')->count();
        $pendingActions = \App\Models\ICS\CorrectiveAction::where('status', 'in_progress')->count();

        return [
            'total_inspections' => $totalInspections,
            'completion_rate' => $totalInspections > 0 ? round(($completedInspections / $totalInspections) * 100, 2) : 0,
            'open_findings' => $openFindings,
            'pending_actions' => $pendingActions,
        ];
    }
}
