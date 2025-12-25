<?php

namespace App\Http\Controllers\Farms;

use App\Http\Controllers\Controller;
use App\Models\Farms\Season;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seasons = Season::orderBy('start_date', 'desc')->get();
        return view('seasons.index', compact('seasons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('seasons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:seasons,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_current' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_current'] = $request->has('is_current');

        // If this season is marked as current, unmark others
        if ($validated['is_current']) {
            Season::where('is_current', true)->update(['is_current' => false]);
        }

        Season::create($validated);

        return redirect()->route('seasons.index')
            ->with('success', 'Season created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Season $season)
    {
        $season->load(['farmRecords', 'farmSeasons']);
        return view('seasons.show', compact('season'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Season $season)
    {
        return view('seasons.edit', compact('season'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $season = Season::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('seasons', 'name')->ignore($id),
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_current' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_current'] = $request->has('is_current');

        // If this season is marked as current, unmark others
        if ($validated['is_current']) {
            Season::where('is_current', true)
                ->where('id', '!=', $season->id)
                ->update(['is_current' => false]);
        }

        $season->update($validated);

        return redirect()->route('seasons.index')
            ->with('success', 'Season updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Season $season)
    {
        // Check if season has any related records
        if ($season->farmRecords()->count() > 0 || $season->farmSeasons()->count() > 0) {
            return redirect()->route('seasons.index')
                ->with('error', 'Cannot delete season with existing records.');
        }

        $season->delete();

        return redirect()->route('seasons.index')
            ->with('success', 'Season deleted successfully.');
    }

    /**
     * Set a season as current.
     */
    public function setCurrent(Season $season)
    {
        $season->markAsCurrent();

        return redirect()->route('seasons.index')
            ->with('success', "Season '{$season->name}' is now the current season.");
    }
}
