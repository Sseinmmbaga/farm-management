<?php

namespace App\Http\Controllers\ICS;

use App\Http\Controllers\Controller;
use App\Models\ICS\Inspection;
use App\Models\Farmers\Farmer;
use App\Models\ICS\InspectionChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Inspection::with(['farmer', 'checklist', 'inspector']);
        
        // Filter by status if requested
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }
        
        // For inspectors, only show their inspections
        /** @var User $user */
        $user = Auth::user();
        if ($user->isIcsInspector()) {
            $query->where('inspector_id', Auth::id());
        }
        
        $inspections = $query->latest()->paginate(20);
        
        return view('ics.inspections.index', compact('inspections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $farmers = Farmer::active()->orderBy('first_name')->get();
        $checklists = InspectionChecklist::active()->orderBy('name')->get();
        
        return view('ics.inspections.create', compact('farmers', 'checklists'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'checklist_id' => 'required|exists:checklists,id',
            'scheduled_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);
        
        $validated['inspector_id'] = Auth::id();
        $validated['status'] = 'scheduled';
        
        try {
            DB::beginTransaction();
            
            $inspection = Inspection::create($validated);
            
            DB::commit();
            
            return redirect()->route('inspections.show', $inspection)
                ->with('success', 'Inspection scheduled successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to schedule inspection: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inspection $inspection)
    {
        $this->authorize('view', $inspection);
        
        $inspection->load(['farmer', 'checklist', 'inspector', 'responses', 'findings']);
        
        return view('ics.inspections.show', compact('inspection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inspection $inspection)
    {
        $this->authorize('update', $inspection);
        
        $farmers = Farmer::active()->orderBy('first_name')->get();
        $checklists = InspectionChecklist::active()->orderBy('name')->get();
        
        return view('ics.inspections.edit', compact('inspection', 'farmers', 'checklists'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inspection $inspection)
    {
        $this->authorize('update', $inspection);
        
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'checklist_id' => 'required|exists:checklists,id',
            'scheduled_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            $inspection->update($validated);
            
            DB::commit();
            
            return redirect()->route('inspections.show', $inspection)
                ->with('success', 'Inspection updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update inspection: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inspection $inspection)
    {
        $this->authorize('delete', $inspection);
        
        try {
            DB::beginTransaction();
            
            $inspection->delete();
            
            DB::commit();
            
            return redirect()->route('inspections.index')
                ->with('success', 'Inspection deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete inspection: ' . $e->getMessage());
        }
    }

    /**
     * Display scheduled inspections.
     */
    public function scheduled()
    {
        $inspections = Inspection::where('status', 'scheduled')
            ->with(['farmer', 'checklist', 'inspector'])
            ->latest('scheduled_date')
            ->paginate(20);
        
        return view('ics.inspections.scheduled', compact('inspections'));
    }

    /**
     * Display in-progress inspections.
     */
    public function inProgress()
    {
        $inspections = Inspection::where('status', 'in_progress')
            ->with(['farmer', 'checklist', 'inspector'])
            ->latest('started_at')
            ->paginate(20);
        
        return view('ics.inspections.in-progress', compact('inspections'));
    }

    /**
     * Display completed inspections.
     */
    public function completed()
    {
        $inspections = Inspection::where('status', 'completed')
            ->with(['farmer', 'checklist', 'inspector'])
            ->latest('completed_at')
            ->paginate(20);
        
        return view('ics.inspections.completed', compact('inspections'));
    }

    /**
     * Start an inspection.
     */
    public function start(Inspection $inspection)
    {
        $this->authorize('update', $inspection);
        
        if ($inspection->status !== 'scheduled') {
            return back()->with('error', 'Only scheduled inspections can be started.');
        }
        
        try {
            DB::beginTransaction();
            
            $inspection->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('inspections.show', $inspection)
                ->with('success', 'Inspection started successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to start inspection: ' . $e->getMessage());
        }
    }

    /**
     * Complete an inspection.
     */
    public function complete(Inspection $inspection)
    {
        $this->authorize('update', $inspection);
        
        if ($inspection->status !== 'in_progress') {
            return back()->with('error', 'Only inspections in progress can be completed.');
        }
        
        try {
            DB::beginTransaction();
            
            $inspection->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('inspections.show', $inspection)
                ->with('success', 'Inspection completed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete inspection: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an inspection.
     */
    public function cancel(Inspection $inspection)
    {
        $this->authorize('update', $inspection);
        
        if (!in_array($inspection->status, ['scheduled', 'in_progress'])) {
            return back()->with('error', 'Only scheduled or in-progress inspections can be cancelled.');
        }
        
        try {
            DB::beginTransaction();
            
            $inspection->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => request('reason'),
            ]);
            
            DB::commit();
            
            return redirect()->route('inspections.index')
                ->with('success', 'Inspection cancelled successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel inspection: ' . $e->getMessage());
        }
    }

    /**
     * Store inspection responses.
     */
    public function storeResponses(Request $request, Inspection $inspection)
    {
        $this->authorize('update', $inspection);
        
        if ($inspection->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Inspection must be in progress to submit responses.'
            ], 400);
        }
        
        $validated = $request->validate([
            'responses' => 'required|array',
            'responses.*.checklist_item_id' => 'required|exists:checklist_items,id',
            'responses.*.response' => 'required|in:compliant,non_compliant,not_applicable',
            'responses.*.notes' => 'nullable|string',
            'responses.*.evidence' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            foreach ($validated['responses'] as $responseData) {
                $inspection->responses()->updateOrCreate(
                    ['checklist_item_id' => $responseData['checklist_item_id']],
                    $responseData
                );
            }
            
            DB::commit();
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save responses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update inspection responses.
     */
    public function updateResponses(Request $request, Inspection $inspection)
    {
        return $this->storeResponses($request, $inspection);
    }

    /**
     * Sign off on inspection.
     */
    public function sign(Inspection $inspection)
    {
        $this->authorize('update', $inspection);
        
        if ($inspection->status !== 'completed') {
            return back()->with('error', 'Only completed inspections can be signed off.');
        }
        
        try {
            DB::beginTransaction();
            
            $inspection->update([
                'signed_by_inspector' => true,
                'signed_by_farmer' => request('farmer_signature') ? true : false,
                'signed_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('inspections.show', $inspection)
                ->with('success', 'Inspection signed off successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to sign off inspection: ' . $e->getMessage());
        }
    }

    /**
     * Schedule follow-up inspection.
     */
    public function scheduleFollowUp(Inspection $inspection)
    {
        $this->authorize('create', Inspection::class);
        
        $validated = request()->validate([
            'scheduled_date' => 'required|date|after:today',
            'reason' => 'required|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create follow-up inspection
            $followUp = Inspection::create([
                'farmer_id' => $inspection->farmer_id,
                'checklist_id' => $inspection->checklist_id,
                'inspector_id' => Auth::id(),
                'scheduled_date' => $validated['scheduled_date'],
                'notes' => $validated['reason'],
                'status' => 'scheduled',
                'parent_inspection_id' => $inspection->id,
            ]);
            
            // Update original inspection
            $inspection->update([
                'has_follow_up' => true,
                'follow_up_inspection_id' => $followUp->id,
            ]);
            
            DB::commit();
            
            return redirect()->route('inspections.show', $followUp)
                ->with('success', 'Follow-up inspection scheduled successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to schedule follow-up: ' . $e->getMessage());
        }
    }

    /**
     * Generate inspection summary report.
     */
    public function reportSummary()
    {
        $this->authorize('viewAny', Inspection::class);
        
        // Get inspection statistics
        $stats = [
            'total' => Inspection::count(),
            'scheduled' => Inspection::where('status', 'scheduled')->count(),
            'in_progress' => Inspection::where('status', 'in_progress')->count(),
            'completed' => Inspection::where('status', 'completed')->count(),
            'cancelled' => Inspection::where('status', 'cancelled')->count(),
        ];
        
        // Get recent inspections
        $recentInspections = Inspection::with(['farmer', 'inspector'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('ics.reports.summary', compact('stats', 'recentInspections'));
    }

    /**
     * Generate compliance report.
     */
    public function reportCompliance()
    {
        $this->authorize('viewAny', Inspection::class);
        
        // This would typically query compliance data
        return view('ics.reports.compliance');
    }

    /**
     * Generate inspector report.
     */
    public function inspectorReport($inspectorId)
    {
        $this->authorize('viewAny', Inspection::class);
        
        $inspections = Inspection::where('inspector_id', $inspectorId)
            ->with(['farmer', 'checklist'])
            ->latest()
            ->paginate(20);
        
        return view('ics.reports.inspector', compact('inspections'));
    }
}