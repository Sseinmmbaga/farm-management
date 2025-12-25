<?php

namespace App\Http\Controllers\ICS;

use App\Http\Controllers\Controller;
use App\Models\ICS\InspectionFinding;
use App\Models\ICS\Inspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FindingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = InspectionFinding::with(['inspection', 'inspection.farmer']);
        
        // Filter by status if requested
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }
        
        // Filter by severity if requested
        if (request()->has('severity')) {
            $query->where('severity', request('severity'));
        }
        
        $findings = $query->latest()->paginate(20);
        
        return view('ics.findings.index', compact('findings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $inspections = Inspection::where('status', 'completed')
            ->with('farmer')
            ->latest()
            ->get();
        
        return view('ics.findings.create', compact('inspections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inspection_id' => 'required|exists:inspections,id',
            'checklist_item_id' => 'nullable|exists:checklist_items,id',
            'finding_type' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:minor,major,critical,observation',
            'evidence' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'due_date' => 'nullable|date|after:today',
        ]);
        
        $validated['status'] = 'open';
        
        try {
            DB::beginTransaction();
            
            $finding = InspectionFinding::create($validated);
            
            DB::commit();
            
            return redirect()->route('findings.show', $finding)
                ->with('success', 'Finding reported successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to report finding: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InspectionFinding $finding)
    {
        $finding->load(['inspection', 'inspection.farmer', 'correctiveActions', 'checklistItem']);
        
        return view('ics.findings.show', compact('finding'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InspectionFinding $finding)
    {
        $inspections = Inspection::where('status', 'completed')
            ->with('farmer')
            ->latest()
            ->get();
        
        return view('ics.findings.edit', compact('finding', 'inspections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InspectionFinding $finding)
    {
        $validated = $request->validate([
            'finding_type' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:minor,major,critical,observation',
            'evidence' => 'nullable|string',
            'recommendation' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);
        
        try {
            DB::beginTransaction();
            
            $finding->update($validated);
            
            DB::commit();
            
            return redirect()->route('findings.show', $finding)
                ->with('success', 'Finding updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update finding: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InspectionFinding $finding)
    {
        try {
            DB::beginTransaction();
            
            $finding->delete();
            
            DB::commit();
            
            return redirect()->route('findings.index')
                ->with('success', 'Finding deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete finding: ' . $e->getMessage());
        }
    }

    /**
     * Display open findings.
     */
    public function open()
    {
        $findings = InspectionFinding::where('status', 'open')
            ->with(['inspection', 'inspection.farmer'])
            ->latest()
            ->paginate(20);
        
        return view('ics.findings.open', compact('findings'));
    }

    /**
     * Display in-progress findings.
     */
    public function inProgress()
    {
        $findings = InspectionFinding::where('status', 'in_progress')
            ->with(['inspection', 'inspection.farmer'])
            ->latest()
            ->paginate(20);
        
        return view('ics.findings.in-progress', compact('findings'));
    }

    /**
     * Display resolved findings.
     */
    public function resolved()
    {
        $findings = InspectionFinding::where('status', 'resolved')
            ->with(['inspection', 'inspection.farmer'])
            ->latest()
            ->paginate(20);
        
        return view('ics.findings.resolved', compact('findings'));
    }

    /**
     * Resolve a finding.
     */
    public function resolve(InspectionFinding $finding)
    {
        $validated = request()->validate([
            'resolution_notes' => 'required|string',
            'resolution_date' => 'required|date',
        ]);
        
        try {
            DB::beginTransaction();
            
            $finding->update([
                'status' => 'resolved',
                'resolution_notes' => $validated['resolution_notes'],
                'resolved_at' => $validated['resolution_date'],
                'resolved_by' => Auth::id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('findings.show', $finding)
                ->with('success', 'Finding resolved successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to resolve finding: ' . $e->getMessage());
        }
    }

    /**
     * Generate findings report.
     */
    public function report()
    {
        $this->authorize('viewAny', InspectionFinding::class);
        
        // Get findings statistics
        $stats = [
            'total' => InspectionFinding::count(),
            'open' => InspectionFinding::where('status', 'open')->count(),
            'in_progress' => InspectionFinding::where('status', 'in_progress')->count(),
            'resolved' => InspectionFinding::where('status', 'resolved')->count(),
            'minor' => InspectionFinding::where('severity', 'minor')->count(),
            'major' => InspectionFinding::where('severity', 'major')->count(),
            'critical' => InspectionFinding::where('severity', 'critical')->count(),
            'observation' => InspectionFinding::where('severity', 'observation')->count(),
        ];
        
        // Get recent findings
        $recentFindings = InspectionFinding::with(['inspection', 'inspection.farmer'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('ics.reports.findings', compact('stats', 'recentFindings'));
    }
}