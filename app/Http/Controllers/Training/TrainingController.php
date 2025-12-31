<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use App\Models\Training\TrainingProgram;
use App\Models\Training\TrainingSession;
use App\Models\Training\TrainingAttendance;
use App\Models\Farmers\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class TrainingController extends Controller
{
    // ==================== TRAINING PROGRAMS ====================

    public function index()
    {
        $programs = TrainingProgram::withCount('sessions')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('training.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('training.programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:training_programs,code',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'duration_hours' => 'nullable|integer|min:1',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();

        TrainingProgram::create($validated);

        return redirect()->route('training.programs.index')
            ->with('success', 'Training program created successfully.');
    }

    public function show($id)
    {
        $program = TrainingProgram::with(['sessions' => function ($query) {
            $query->orderBy('scheduled_date', 'desc');
        }])->findOrFail($id);

        return view('training.programs.show', compact('program'));
    }

    public function edit($id)
    {
        $program = TrainingProgram::findOrFail($id);
        return view('training.programs.edit', compact('program'));
    }

    public function update(Request $request, $id)
    {
        $program = TrainingProgram::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_sw' => 'nullable|string|max:255',
            'code' => 'required|string|max:50|unique:training_programs,code,' . $program->id,
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'duration_hours' => 'nullable|integer|min:1',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $program->update($validated);

        return redirect()->route('training.programs.show', $program)
            ->with('success', 'Training program updated successfully.');
    }

    public function destroy($id)
    {
        $program = TrainingProgram::findOrFail($id);

        // Prevent deletion if there are sessions
        if ($program->sessions()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete program because it has training sessions.');
        }

        $program->delete();

        return redirect()->route('training.programs.index')
            ->with('success', 'Training program deleted successfully.');
    }

    // ==================== TRAINING SESSIONS ====================

    public function upcoming()
    {
        $sessions = TrainingSession::with('program')
            ->upcoming()
            ->orderBy('scheduled_date', 'asc')
            ->paginate(20);

        return view('training.sessions.upcoming', compact('sessions'));
    }

    public function completed()
    {
        $sessions = TrainingSession::with('program')
            ->completed()
            ->orderBy('scheduled_date', 'desc')
            ->paginate(20);

        return view('training.sessions.completed', compact('sessions'));
    }

    public function cancel($session)
    {
        $session = TrainingSession::findOrFail($session);

        $request = request();
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $session->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()->route('training.sessions.upcoming')
            ->with('success', 'Training session cancelled.');
    }

    public function complete($session)
    {
        $session = TrainingSession::findOrFail($session);

        $session->update([
            'status' => 'completed',
            'end_date' => now(),
        ]);

        // Update attendance records
        TrainingAttendance::where('training_session_id', $session->id)
            ->where('attended', true)
            ->update(['completed' => true]);

        return redirect()->route('training.completed')
            ->with('success', 'Training session marked as completed.');
    }

    public function issueCertificates($session)
    {
        $session = TrainingSession::with('attendances.farmer')->findOrFail($session);

        // Ensure session is completed
        if ($session->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'Certificates can only be issued for completed sessions.');
        }

        DB::transaction(function () use ($session) {
            foreach ($session->attendances as $attendance) {
                if ($attendance->attended && !$attendance->certificate_issued) {
                    // Generate certificate number
                    $certNumber = 'CERT-' . strtoupper(uniqid());

                    // Create certificate
                    \App\Models\Training\TrainingCertificate::create([
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
                }
            }
        });

        return redirect()->route('training.sessions.show', $session)
            ->with('success', 'Certificates issued to eligible attendees.');
    }

    // ==================== CERTIFICATES ====================

    public function certificates()
    {
        $certificates = \App\Models\Training\TrainingCertificate::with(['farmer', 'program', 'session', 'issuedBy'])
            ->orderBy('issue_date', 'desc')
            ->paginate(30);

        return view('training.certificates.index', compact('certificates'));
    }

    public function showCertificate($certificate)
    {
        $certificate = \App\Models\Training\TrainingCertificate::with(['farmer', 'program', 'session', 'issuedBy'])
            ->findOrFail($certificate);

        return view('training.certificates.show', compact('certificate'));
    }

    public function downloadCertificate($certificate)
    {
        $certificate = \App\Models\Training\TrainingCertificate::with(['farmer', 'program', 'session', 'issuedBy'])->findOrFail($certificate);

        // If PDF already exists, serve it
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            return response()->download(
                Storage::disk('public')->path($certificate->file_path),
                'certificate_' . $certificate->certificate_number . '.pdf',
                ['Content-Type' => 'application/pdf']
            );
        }

        // Generate PDF
        $pdf = PDF::loadView('training.certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont' => 'Helvetica',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        // Save PDF to storage
        $fileName = 'certificates/' . $certificate->id . '_' . $certificate->certificate_number . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        // Update certificate file path
        $certificate->update(['file_path' => $fileName]);

        // Return download response
        return $pdf->download('certificate_' . $certificate->certificate_number . '.pdf');
    }

    // ==================== REPORTS ====================

    public function reportSummary()
    {
        $totalPrograms = TrainingProgram::count();
        $totalSessions = TrainingSession::count();
        $upcomingSessions = TrainingSession::upcoming()->count();
        $completedSessions = TrainingSession::completed()->count();
        $totalParticipants = TrainingAttendance::count();
        $totalAttended = TrainingAttendance::where('attended', true)->count();

        $programs = TrainingProgram::withCount(['sessions', 'attendances'])
            ->orderBy('sessions_count', 'desc')
            ->take(10)
            ->get();

        return view('training.reports.summary', compact(
            'totalPrograms',
            'totalSessions',
            'upcomingSessions',
            'completedSessions',
            'totalParticipants',
            'totalAttended',
            'programs'
        ));
    }

    public function reportAttendance()
    {
        $sessions = TrainingSession::withCount(['attendances', 'attendances as attended_count' => function ($query) {
            $query->where('attended', true);
        }])
            ->orderBy('scheduled_date', 'desc')
            ->paginate(20);

        return view('training.reports.attendance', compact('sessions'));
    }

    public function farmerTrainingHistory($farmer)
    {
        $farmer = Farmer::with(['trainingAttendances.session.program'])->findOrFail($farmer);

        $attendances = $farmer->trainingAttendances()
            ->with('session.program')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('training.reports.farmer', compact('farmer', 'attendances'));
    }
}