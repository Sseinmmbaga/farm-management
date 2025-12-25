<?php

namespace App\Http\Controllers\ICS;

use App\Http\Controllers\Controller;
use App\Models\ICS\CorrectiveAction;
use App\Models\ICS\InspectionFinding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CorrectiveActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = CorrectiveAction::with(['finding', 'finding.inspection', 'responsiblePerson']);
        
        // Filter by status if requested
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }
        
        $actions = $query->latest()->paginate(20);
        
        return view('ics.corrective-actions.index', compact('actions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $findings = InspectionFinding::where('status', '!=', 'resolved')
            ->where('status', '!=', 'closed')
            ->with('inspection.farmer')
            ->latest()
            ->get();
        
        return view('ics.corrective-actions.create', compact('findings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'finding_id' => 'required|exists:findings,id',
            'description' => 'required|string',
            'description_sw' => 'nullable|string',
            'action_type' => 'required|in:correction,corrective_action,preventive_action',
            'responsible_person_id' => 'required|exists:users,id',
            'planned_date' => 'required|date|after:today',
            'status' => 'required|in:planned,in_progress,completed,verified,ineffective',
        ]);
        
        $validated['created_by'] = Auth::id();
        
        try {
            DB::beginTransaction();
            
            $action = CorrectiveAction::create($validated);
            
            DB::commit();
            
            return redirect()->route('corrective-actions.show', $action)
                ->with('success', 'Corrective action created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create corrective action: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CorrectiveAction $correctiveAction)
    {
        $correctiveAction->load([
            'finding', 
            'finding.inspection', 
            'finding.inspection.farmer',
            'responsiblePerson',
            'createdBy',
            'verifiedBy'
        ]);
        
        return view('ics.corrective-actions.show', compact('correctiveAction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CorrectiveAction $correctiveAction)
    {
        $findings = InspectionFinding::where('status', '!=', 'resolved')
            ->where('status', '!=', 'closed')
            ->with('inspection.farmer')
            ->latest()
            ->get();
        
        return view('ics.corrective-actions.edit', compact('correctiveAction', 'findings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CorrectiveAction $correctiveAction)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'description_sw' => 'nullable|string',
            'action_type' => 'required|in:correction,corrective_action,preventive_action',
            'responsible_person_id' => 'required|exists:users,id',
            'planned_date' => 'required|date',
            'status' => 'required|in:planned,in_progress,completed,verified,ineffective',
            'verification_notes' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            $correctiveAction->update($validated);
            
            DB::commit();
            
            return redirect()->route('corrective-actions.show', $correctiveAction)
                ->with('success', 'Corrective action updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update corrective action: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CorrectiveAction $correctiveAction)
    {
        try {
            DB::beginTransaction();
            
            $correctiveAction->delete();
            
            DB::commit();
            
            return redirect()->route('corrective-actions.index')
                ->with('success', 'Corrective action deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete corrective action: ' . $e->getMessage());
        }
    }

    /**
     * Complete a corrective action.
     */
    public function complete(CorrectiveAction $action)
    {
        $validated = request()->validate([
            'completion_notes' => 'required|string',
            'completion_date' => 'required|date',
            'evidence_of_completion' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            $action->update([
                'status' => 'completed',
                'completion_notes' => $validated['completion_notes'],
                'completion_date' => $validated['completion_date'],
                'evidence_of_completion' => $validated['evidence_of_completion'],
            ]);
            
            DB::commit();
            
            return redirect()->route('corrective-actions.show', $action)
                ->with('success', 'Corrective action marked as completed!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete corrective action: ' . $e->getMessage());
        }
    }

    /**
     * Verify a corrective action.
     */
    public function verify(CorrectiveAction $action)
    {
        if ($action->status !== 'completed') {
            return back()->with('error', 'Only completed corrective actions can be verified.');
        }
        
        try {
            DB::beginTransaction();
            
            $action->update([
                'status' => 'verified',
                'verified' => true,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_notes' => request('verification_notes'),
                'is_effective' => request('is_effective', true),
            ]);
            
            DB::commit();
            
            return redirect()->route('corrective-actions.show', $action)
                ->with('success', 'Corrective action verified successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to verify corrective action: ' . $e->getMessage());
        }
    }
}