<?php

namespace App\Http\Controllers\Farmers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmers\StoreFarmerDocumentRequest;
use App\Http\Requests\Farmers\UpdateFarmerDocumentRequest;
use App\Models\Farmers\FarmerDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FarmerDocumentController extends Controller
{
    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        $farmer = request()->route('farmer');
        $documentTypes = FarmerDocument::getTypes();

        return view('farmers.documents.create', compact('farmer', 'documentTypes'));
    }

    /**
     * Store a newly created document.
     */
    public function store(StoreFarmerDocumentRequest $request)
    {
        $farmer = $request->route('farmer');
        $validated = $request->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('farmer-documents', 'public');
            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $validated['uploaded_by'] = Auth::id();
        $validated['farmer_id'] = $farmer->id;

        $document = FarmerDocument::create($validated);

        return redirect()
            ->route('farmers.show', $farmer)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(string $id)
    {
        $document = FarmerDocument::with('farmer')->findOrFail($id);
        $farmer = $document->farmer;
        $documentTypes = FarmerDocument::getTypes();

        return view('farmers.documents.edit', compact('document', 'farmer', 'documentTypes'));
    }

    /**
     * Update the specified document.
     */
    public function update(UpdateFarmerDocumentRequest $request, string $id)
    {
        $document = FarmerDocument::findOrFail($id);
        $farmer = $document->farmer;
        $validated = $request->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('farmer-documents', 'public');
            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $document->update($validated);

        return redirect()
            ->route('farmers.show', $farmer)
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified document.
     */
    public function destroy(string $id)
    {
        $document = FarmerDocument::findOrFail($id);
        $farmer = $document->farmer;

        // Delete file if exists
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('farmers.show', $farmer)
            ->with('success', 'Document deleted successfully.');
    }
}
