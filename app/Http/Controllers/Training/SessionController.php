<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use App\Models\Training\TrainingProgram;
use App\Models\Training\TrainingSession;
use App\Models\Training\TrainingAttendance;
use App\Models\Training\TrainingCertificate;
use App\Models\Farms\Season;
use App\Models\Location\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    /**
     * Display a listing of all sessions.
     */
    public function index()
    {
        $sessions = TrainingSession::with('program')
            ->orderBy('scheduled_date', 'desc')
            ->paginate(20);

        return view('training.sessions.index', compact('sessions'));
    }

    /**
     * Display upcoming sessions.
     */
    public function upcoming()
    {
        $sessions = TrainingSession::with('program')
            ->upcoming()
            ->orderBy('scheduled_date', 'asc')
            ->paginate(20);

        $programs = TrainingProgram::active()->orderBy('name')->get();

        $thisWeekCount = TrainingSession::upcoming()
            ->whereBetween('scheduled_date', [now(), now()->endOfWeek()])
            ->count();

        return view('training.sessions.upcoming', compact('sessions', 'programs', 'thisWeekCount'));
    }

    /**
     * Display completed sessions.
     */
    public function completed()
    {
        $sessions = TrainingSession::with('program')
            ->completed()
            ->orderBy('scheduled_date', 'desc')
            ->paginate(20);

        $programs = TrainingProgram::orderBy('name')->get();

        return view('training.sessions.completed', compact('sessions', 'programs'));
    }

    /**
     * Show the form for creating a new session.
     */
    public function create()
    {
        $programs = TrainingProgram::active()->orderBy('name')->get();
        $seasons = Season::orderBy('start_date', 'desc')->get();
        $regions = Region::orderBy('name')->get();
        $trainers = User::whereIn('role', ['admin', 'training_coordinator', 'extension_officer'])
            ->orderBy('name')
            ->get();

        return view('training.sessions.create', compact('programs', 'seasons', 'regions', 'trainers'));
    }

    /**
     * Store a newly created session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date|after:now',
            'duration_hours' => 'nullable|integer|min:1',
            'venue' => 'required|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'trainer_id' => 'nullable|exists:users,id',
            'trainer_name' => 'nullable|string|max:255',
            'trainer_organization' => 'nullable|string|max:255',
            'trainer_contact' => 'nullable|string|max:100',
            'max_participants' => 'nullable|integer|min:1',
            'season_id' => 'nullable|exists:seasons,id',
        ]);

        $validated['status'] = 'scheduled';
        $validated['created_by'] = Auth::id();

        $session = TrainingSession::create($validated);

        return redirect()->route('training.sessions.show', $session)
            ->with('success', 'Training session scheduled successfully.');
    }

    /**
     * Display the specified session.
     */
    public function show($id)
    {
        $session = TrainingSession::with([
            'program',
            'trainer',
            'season',
            'attendances.farmer',
            'region',
            'district',
            'village'
        ])->findOrFail($id);

        return view('training.sessions.show', compact('session'));
    }

    /**
     * Show the form for editing the specified session.
     */
    public function edit($id)
    {
        $session = TrainingSession::findOrFail($id);
        $programs = TrainingProgram::active()->orderBy('name')->get();
        $seasons = Season::orderBy('start_date', 'desc')->get();
        $regions = Region::orderBy('name')->get();
        $trainers = User::whereIn('role', ['admin', 'training_coordinator', 'extension_officer'])
            ->orderBy('name')
            ->get();

        // Get districts if region is selected
        $districts = [];
        if ($session->region_id) {
            $districts = \App\Models\District::where('region_id', $session->region_id)
                ->orderBy('name')
                ->get();
        }

        return view('training.sessions.edit', compact('session', 'programs', 'seasons', 'regions', 'districts', 'trainers'));
    }

    /**
     * Update the specified session.
     */
    public function update(Request $request, $id)
    {
        $session = TrainingSession::findOrFail($id);

        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'duration_hours' => 'nullable|integer|min:1',
            'venue' => 'required|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'village_id' => 'nullable|exists:villages,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'trainer_id' => 'nullable|exists:users,id',
            'trainer_name' => 'nullable|string|max:255',
            'trainer_organization' => 'nullable|string|max:255',
            'trainer_contact' => 'nullable|string|max:100',
            'max_participants' => 'nullable|integer|min:1',
            'season_id' => 'nullable|exists:seasons,id',
            'status' => 'in:scheduled,in_progress,completed,cancelled,postponed',
        ]);

        $validated['updated_by'] = Auth::id();

        $session->update($validated);

        return redirect()->route('training.sessions.show', $session)
            ->with('success', 'Training session updated successfully.');
    }

    /**
     * Remove the specified session.
     */
    public function destroy($id)
    {
        $session = TrainingSession::findOrFail($id);

        // Prevent deletion if there are attendances
        if ($session->attendances()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete session because it has attendance records. Cancel it instead.');
        }

        $session->delete();

        return redirect()->route('training.upcoming')
            ->with('success', 'Training session deleted successfully.');
    }

    /**
     * Cancel a session.
     */
    public function cancel(Request $request, $session)
    {
        $session = TrainingSession::findOrFail($session);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $session->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('training.upcoming')
            ->with('success', 'Training session cancelled.');
    }

    /**
     * Mark session as complete.
     */
    public function complete($session)
    {
        $session = TrainingSession::findOrFail($session);

        $session->update([
            'status' => 'completed',
            'end_date' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Update attendance records
        TrainingAttendance::where('training_session_id', $session->id)
            ->where('attended', true)
            ->update(['completed' => true]);

        return redirect()->route('training.completed')
            ->with('success', 'Training session marked as completed.');
    }

    /**
     * Issue certificates to attended farmers.
     */
    public function issueCertificates($session)
    {
        $session = TrainingSession::with('attendances.farmer')->findOrFail($session);

        // Ensure session is completed
        if ($session->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'Certificates can only be issued for completed sessions.');
        }

        $issuedCount = 0;

        DB::transaction(function () use ($session, &$issuedCount) {
            foreach ($session->attendances as $attendance) {
                if ($attendance->attended && !$attendance->certificate_issued) {
                    // Generate certificate number
                    $certNumber = 'CERT-' . strtoupper(uniqid());

                    // Create certificate
                    TrainingCertificate::create([
                        'farmer_id' => $attendance->farmer_id,
                        'training_program_id' => $session->training_program_id,
                        'training_session_id' => $session->id,
                        'certificate_number' => $certNumber,
                        'issue_date' => now(),
                        'expiry_date' => now()->addYears(2),
                        'status' => 'active',
                        'issued_by' => Auth::id(),
                    ]);

                    // Mark attendance as certificate issued
                    $attendance->update([
                        'certificate_issued' => true,
                        'certificate_number' => $certNumber,
                        'certificate_date' => now(),
                    ]);

                    $issuedCount++;
                }
            }
        });

        return redirect()->route('training.sessions.show', $session)
            ->with('success', "{$issuedCount} certificates issued to eligible attendees.");
    }
}
