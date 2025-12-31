<?php

namespace App\Http\Controllers\Visits;

use App\Http\Controllers\Controller;
use App\Http\Requests\Visits\StoreFarmVisitRequest;
use App\Http\Requests\Visits\UpdateFarmVisitRequest;
use App\Models\Farms\FarmVisit;
use App\Models\Farms\Farm;
use App\Models\Farmers\Farmer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FarmVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $visits = FarmVisit::with(['farm', 'farmer', 'supervisor'])
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->farm_id, fn($q, $id) => $q->forFarm($id))
            ->when($request->farmer_id, fn($q, $id) => $q->forFarmer($id))
            ->when($request->supervisor_id, fn($q, $id) => $q->forSupervisor($id))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->purpose, fn($q, $purpose) => $q->where('purpose', $purpose))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('scheduled_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('scheduled_date', '<=', $date))
            ->latest()
            ->paginate(20);

        $farms = Farm::active()->get(['id', 'code', 'name']);
        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);
        $supervisors = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['supervisor', 'extension_officer', 'admin']);
        })->get(['id', 'name', 'email']);

        return view('visits.index', compact('visits', 'farms', 'farmers', 'supervisors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $farms = Farm::active()->get(['id', 'code', 'name']);
        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);
        $supervisors = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['supervisor', 'extension_officer', 'admin']);
        })->get(['id', 'name', 'email']);

        return view('visits.create', compact('farms', 'farmers', 'supervisors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFarmVisitRequest $request)
    {
        $validated = $request->validated();
        $photos = $request->file('photos');

        // Remove photos from validated data because we'll handle separately
        unset($validated['photos']);

        // Create the visit
        $visit = FarmVisit::create($validated);

        // Handle photo uploads
        $photoPaths = [];
        if ($photos) {
            foreach ($photos as $photo) {
                $path = $photo->store('visits/photos', 'public');
                $photoPaths[] = $path;
            }
        }

        // Update visit with photo paths and has_photos flag
        $visit->update([
            'photos' => $photoPaths,
            'has_photos' => !empty($photoPaths),
        ]);

        return redirect()
            ->route('visits.show', $visit)
            ->with('success', 'Farm visit scheduled successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $visit = FarmVisit::with(['farm', 'field', 'farmer', 'supervisor', 'region', 'district', 'village'])
            ->findOrFail($id);

        return view('visits.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $visit = FarmVisit::findOrFail($id);
        $farms = Farm::active()->get(['id', 'code', 'name']);
        $farmers = Farmer::active()->get(['id', 'first_name', 'last_name', 'registration_number']);
        $supervisors = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['supervisor', 'extension_officer', 'admin']);
        })->get(['id', 'name', 'email']);

        return view('visits.edit', compact('visit', 'farms', 'farmers', 'supervisors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFarmVisitRequest $request, $id)
    {
        $visit = FarmVisit::findOrFail($id);
        $validated = $request->validated();
        $photos = $request->file('photos');
        $deletePhotos = $request->input('delete_photos', []);

        // Remove photos from validated data because we'll handle separately
        unset($validated['photos']);

        // Process existing photos: remove deleted ones
        $existingPhotos = $visit->photos ?? [];
        $updatedPhotos = array_filter($existingPhotos, function ($path) use ($deletePhotos) {
            return !in_array($path, $deletePhotos);
        });

        // Delete removed photos from storage
        foreach ($deletePhotos as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Handle new photo uploads
        if ($photos) {
            foreach ($photos as $photo) {
                $path = $photo->store('visits/photos', 'public');
                $updatedPhotos[] = $path;
            }
        }

        // Update visit with merged data
        $visit->update(array_merge($validated, [
            'photos' => array_values($updatedPhotos),
            'has_photos' => !empty($updatedPhotos),
        ]));

        return redirect()
            ->route('visits.show', $visit)
            ->with('success', 'Farm visit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $visit = FarmVisit::findOrFail($id);
        $visit->delete();

        return redirect()
            ->route('visits.index')
            ->with('success', 'Farm visit deleted successfully.');
    }

    /**
     * Calendar view for scheduled visits.
     */
    public function calendar()
    {
        $visits = FarmVisit::where('status', 'scheduled')
            ->orWhere('status', 'in_progress')
            ->get(['id', 'visit_number', 'farm_id', 'scheduled_date', 'status', 'purpose']);

        $events = $visits->map(function ($visit) {
            return [
                'id' => $visit->id,
                'title' => $visit->visit_number . ' - ' . ($visit->farm?->code ?? 'Farm'),
                'start' => $visit->scheduled_date,
                'url' => route('visits.show', $visit),
                'color' => $visit->status === 'scheduled' ? '#3b82f6' : '#f59e0b',
            ];
        });

        return view('visits.calendar', compact('events'));
    }

    /**
     * Mark visit as completed.
     */
    public function complete($id)
    {
        $visit = FarmVisit::findOrFail($id);
        $visit->markAsCompleted();

        return redirect()
            ->route('visits.show', $visit)
            ->with('success', 'Visit marked as completed.');
    }

    /**
     * Mark visit as in progress.
     */
    public function start($id)
    {
        $visit = FarmVisit::findOrFail($id);
        $visit->markAsInProgress();

        return redirect()
            ->route('visits.show', $visit)
            ->with('success', 'Visit marked as in progress.');
    }

    /**
     * Cancel a visit.
     */
    public function cancel($id)
    {
        $visit = FarmVisit::findOrFail($id);
        $visit->cancel();

        return redirect()
            ->route('visits.show', $visit)
            ->with('success', 'Visit cancelled.');
    }

    /**
     * Export visits as CSV.
     */
    public function export(Request $request)
    {
        $visits = FarmVisit::with(['farm', 'farmer', 'supervisor'])
            ->when($request->date_from, fn($q, $date) => $q->whereDate('scheduled_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('scheduled_date', '<=', $date))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="farm_visits_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($visits) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'Visit Number',
                'Farm Code',
                'Farmer Name',
                'Supervisor',
                'Scheduled Date',
                'Actual Date',
                'Status',
                'Purpose',
                'Notes',
                'Photos Count',
                'Report',
                'Created At',
            ]);

            // CSV Data
            foreach ($visits as $visit) {
                fputcsv($file, [
                    $visit->visit_number,
                    $visit->farm?->code ?? 'N/A',
                    $visit->farmer?->full_name ?? 'N/A',
                    $visit->supervisor?->name ?? 'N/A',
                    $visit->scheduled_date?->format('Y-m-d H:i'),
                    $visit->actual_date?->format('Y-m-d H:i'),
                    $visit->status_label,
                    $visit->purpose_label,
                    $visit->notes ?? '',
                    $visit->has_photos ? count($visit->photos) : 0,
                    $visit->report ?? '',
                    $visit->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
