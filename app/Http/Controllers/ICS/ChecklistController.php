<?php

namespace App\Http\Controllers\ICS;

use App\Http\Controllers\Controller;
use App\Models\ICS\InspectionChecklist;
use App\Models\ICS\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $checklists = InspectionChecklist::with(['items' => function ($query) {
            $query->orderBy('sort_order');
        }])->latest()->paginate(20);
        
        return view('ics.checklists.index', compact('checklists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ics.checklists.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->boolean('is_active');
        
        try {
            DB::beginTransaction();
            
            $checklist = InspectionChecklist::create($validated);
            
            DB::commit();
            
            return redirect()->route('checklists.show', $checklist)
                ->with('success', 'Checklist created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create checklist: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InspectionChecklist $checklist)
    {
        $checklist->load(['items' => function ($query) {
            $query->orderBy('sort_order');
        }]);
        
        return view('ics.checklists.show', compact('checklist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InspectionChecklist $checklist)
    {
        return view('ics.checklists.edit', compact('checklist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InspectionChecklist $checklist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->boolean('is_active');
        
        try {
            DB::beginTransaction();
            
            $checklist->update($validated);
            
            DB::commit();
            
            return redirect()->route('checklists.show', $checklist)
                ->with('success', 'Checklist updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update checklist: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InspectionChecklist $checklist)
    {
        try {
            DB::beginTransaction();
            
            $checklist->delete();
            
            DB::commit();
            
            return redirect()->route('checklists.index')
                ->with('success', 'Checklist deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete checklist: ' . $e->getMessage());
        }
    }

    /**
     * Store a new checklist item.
     */
    public function storeItem(Request $request, InspectionChecklist $checklist)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'description' => 'nullable|string',
            'requirement' => 'nullable|string',
            'reference' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0|max:100',
            'is_critical' => 'boolean',
        ]);
        
        $validated['is_critical'] = $request->boolean('is_critical');
        $validated['sort_order'] = $checklist->items()->max('sort_order') + 1;
        
        try {
            DB::beginTransaction();
            
            $checklist->items()->create($validated);
            
            DB::commit();
            
            return redirect()->route('checklists.show', $checklist)
                ->with('success', 'Checklist item added successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to add checklist item: ' . $e->getMessage());
        }
    }

    /**
     * Update a checklist item.
     */
    public function updateItem(Request $request, InspectionChecklist $checklist, ChecklistItem $item)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'description' => 'nullable|string',
            'requirement' => 'nullable|string',
            'reference' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0|max:100',
            'is_critical' => 'boolean',
        ]);
        
        $validated['is_critical'] = $request->boolean('is_critical');
        
        try {
            DB::beginTransaction();
            
            $item->update($validated);
            
            DB::commit();
            
            return redirect()->route('checklists.show', $checklist)
                ->with('success', 'Checklist item updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update checklist item: ' . $e->getMessage());
        }
    }

    /**
     * Remove a checklist item.
     */
    public function destroyItem(InspectionChecklist $checklist, ChecklistItem $item)
    {
        try {
            DB::beginTransaction();
            
            $item->delete();
            
            DB::commit();
            
            return redirect()->route('checklists.show', $checklist)
                ->with('success', 'Checklist item deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete checklist item: ' . $e->getMessage());
        }
    }

    /**
     * Reorder checklist items.
     */
    public function reorderItems(Request $request, InspectionChecklist $checklist)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:checklist_items,id',
            'items.*.order' => 'required|integer|min:0',
        ]);
        
        try {
            DB::beginTransaction();
            
            foreach ($request->items as $itemData) {
                ChecklistItem::where('id', $itemData['id'])
                    ->where('checklist_id', $checklist->id)
                    ->update(['sort_order' => $itemData['order']]);
            }
            
            DB::commit();
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
