<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use App\Models\Training\TrainingMaterial;
use App\Models\Training\TrainingProgram;
use App\Models\Training\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrainingMaterialController extends Controller
{
    /**
     * Show the form for creating a new material.
     */
    public function create()
    {
        $programs = TrainingProgram::active()->orderBy('name')->get();
        $sessions = TrainingSession::with('program')
            ->orderBy('scheduled_date', 'desc')
            ->take(50)
            ->get();

        return view('training.materials.create', compact('programs', 'sessions'));
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_sw' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:document,video,presentation,handout,other',
            'training_program_id' => 'nullable|exists:training_programs,id',
            'training_session_id' => 'nullable|exists:training_sessions,id',
            'file' => 'nullable|file|max:51200', // 50MB max
            'external_url' => 'nullable|url',
            'language' => 'in:sw,en',
            'is_downloadable' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('training-materials', 'public');
            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $validated['uploaded_by'] = Auth::id();
        $validated['is_active'] = true;

        $material = TrainingMaterial::create($validated);

        $redirect = $material->training_program_id
            ? route('training.materials.program', $material->training_program_id)
            : route('training.materials.show', $material);

        return redirect($redirect)
            ->with('success', 'Training material uploaded successfully.');
    }

    /**
     * Display the specified material.
     */
    public function show($id)
    {
        $material = TrainingMaterial::with(['program', 'session', 'uploadedBy'])->findOrFail($id);

        return view('training.materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit($id)
    {
        $material = TrainingMaterial::findOrFail($id);
        $programs = TrainingProgram::active()->orderBy('name')->get();
        $sessions = TrainingSession::with('program')
            ->orderBy('scheduled_date', 'desc')
            ->take(50)
            ->get();

        return view('training.materials.edit', compact('material', 'programs', 'sessions'));
    }

    /**
     * Update the specified material.
     */
    public function update(Request $request, $id)
    {
        $material = TrainingMaterial::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_sw' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:document,video,presentation,handout,other',
            'training_program_id' => 'nullable|exists:training_programs,id',
            'training_session_id' => 'nullable|exists:training_sessions,id',
            'file' => 'nullable|file|max:51200',
            'external_url' => 'nullable|url',
            'language' => 'in:sw,en',
            'is_downloadable' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('training-materials', 'public');
            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $material->update($validated);

        return redirect()->route('training.materials.show', $material)
            ->with('success', 'Training material updated successfully.');
    }

    /**
     * Remove the specified material.
     */
    public function destroy($id)
    {
        $material = TrainingMaterial::findOrFail($id);
        $programId = $material->training_program_id;

        // Delete file if exists
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        $redirect = $programId
            ? route('training.materials.program', $programId)
            : route('training-programs.index');

        return redirect($redirect)
            ->with('success', 'Training material deleted successfully.');
    }

    /**
     * Display materials for a specific program.
     */
    public function programMaterials($program)
    {
        $program = TrainingProgram::findOrFail($program);
        $materials = TrainingMaterial::where('training_program_id', $program->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('training.materials.program', compact('program', 'materials'));
    }

    /**
     * Download a material file.
     */
    public function download($id)
    {
        $material = TrainingMaterial::findOrFail($id);

        if (!$material->file_path || !$material->is_downloadable) {
            return redirect()->back()->with('error', 'File not available for download.');
        }

        // Increment download count
        $material->increment('download_count');

        return Storage::disk('public')->download($material->file_path, $material->title . '.' . pathinfo($material->file_path, PATHINFO_EXTENSION));
    }
}
