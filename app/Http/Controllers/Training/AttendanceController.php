<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use App\Models\Training\TrainingAttendance;
use App\Models\Training\TrainingSession;
use App\Models\Farmers\Farmer;
use App\Notifications\TrainingRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a list of all training sessions for attendance management.
     */
    public function list(Request $request)
    {
        $query = TrainingSession::with('program')
            ->withCount(['attendances', 'attendances as attended_count' => function ($q) {
                $q->where('attended', true);
            }]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('scheduled_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('scheduled_date', '<=', $request->to_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhereHas('program', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sessions = $query->orderBy('scheduled_date', 'desc')->paginate(20)->withQueryString();

        return view('training.attendance.list', compact('sessions'));
    }

    public function index($session)
    {
        $session = TrainingSession::with(['program', 'attendances.farmer'])->findOrFail($session);
        $attendances = $session->attendances()->with('farmer')->paginate(50);

        // Get farmers not yet registered for this session
        $registeredFarmerIds = $attendances->pluck('farmer_id')->toArray();
        $availableFarmers = Farmer::whereNotIn('id', $registeredFarmerIds)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'registration_number']);

        return view('training.attendance.index', compact('session', 'attendances', 'availableFarmers'));
    }

    public function store(Request $request, $session)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'registration_status' => 'in:registered,waitlist,cancelled',
            'notes' => 'nullable|string',
        ]);

        $session = TrainingSession::findOrFail($session);

        // Check if already registered
        $existing = TrainingAttendance::where('training_session_id', $session->id)
            ->where('farmer_id', $validated['farmer_id'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Farmer is already registered for this session.');
        }

        $farmer = Farmer::findOrFail($validated['farmer_id']);

        TrainingAttendance::create([
            'training_session_id' => $session->id,
            'farmer_id' => $validated['farmer_id'],
            'registration_status' => $validated['registration_status'] ?? 'registered',
            'recorded_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update registered count
        $session->increment('registered_count');

        // Send notification to farmer (via their user account) and extension officer
        $this->sendRegistrationNotifications($session, $farmer);

        return redirect()->route('training.attendance.index', $session)
            ->with('success', 'Farmer registered for training session.');
    }

    public function update(Request $request, $session, $attendance)
    {
        $attendance = TrainingAttendance::where('training_session_id', $session)
            ->findOrFail($attendance);

        $validated = $request->validate([
            'registration_status' => 'in:registered,waitlist,cancelled',
            'attended' => 'boolean',
            'check_in_time' => 'nullable|date',
            'check_out_time' => 'nullable|date',
            'score' => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        // Update attended count if attended changed
        if ($request->has('attended')) {
            $session = TrainingSession::findOrFail($session);
            if ($validated['attended'] && !$attendance->attended) {
                $session->increment('attended_count');
            } elseif (!$validated['attended'] && $attendance->attended) {
                $session->decrement('attended_count');
            }
        }

        return redirect()->route('training.attendance.index', $session)
            ->with('success', 'Attendance updated.');
    }

    public function bulkStore(Request $request, $session)
    {
        $validated = $request->validate([
            'farmer_ids' => 'required|array',
            'farmer_ids.*' => 'exists:farmers,id',
            'registration_status' => 'in:registered,waitlist,cancelled',
        ]);

        $session = TrainingSession::findOrFail($session);

        $existingCount = TrainingAttendance::where('training_session_id', $session->id)
            ->whereIn('farmer_id', $validated['farmer_ids'])
            ->count();

        if ($existingCount > 0) {
            return redirect()->back()
                ->with('error', 'Some farmers are already registered.');
        }

        // Get farmers for notifications
        $farmers = Farmer::whereIn('id', $validated['farmer_ids'])->get();

        DB::transaction(function () use ($session, $validated) {
            $records = [];
            foreach ($validated['farmer_ids'] as $farmerId) {
                $records[] = [
                    'training_session_id' => $session->id,
                    'farmer_id' => $farmerId,
                    'registration_status' => $validated['registration_status'] ?? 'registered',
                    'recorded_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            TrainingAttendance::insert($records);

            // Update registered count
            $session->increment('registered_count', count($records));
        });

        // Send notifications to all registered farmers
        foreach ($farmers as $farmer) {
            $this->sendRegistrationNotifications($session, $farmer);
        }

        return redirect()->route('training.attendance.index', $session)
            ->with('success', count($validated['farmer_ids']) . ' farmers registered.');
    }

    public function checkIn($session, $farmer)
    {
        $attendance = TrainingAttendance::where('training_session_id', $session)
            ->where('farmer_id', $farmer)
            ->firstOrFail();

        if ($attendance->check_in_time) {
            return redirect()->back()
                ->with('warning', 'Farmer already checked in.');
        }

        $attendance->update([
            'check_in_time' => now(),
            'attended' => true,
        ]);

        // Update attended count if not already counted
        if (!$attendance->attended) {
            TrainingSession::findOrFail($session)->increment('attended_count');
        }

        return redirect()->route('training.attendance.index', $session)
            ->with('success', 'Check-in recorded.');
    }

    public function checkOut($session, $farmer)
    {
        $attendance = TrainingAttendance::where('training_session_id', $session)
            ->where('farmer_id', $farmer)
            ->firstOrFail();

        if (!$attendance->check_in_time) {
            return redirect()->back()
                ->with('error', 'Farmer must check in before checking out.');
        }

        if ($attendance->check_out_time) {
            return redirect()->back()
                ->with('warning', 'Farmer already checked out.');
        }

        $attendance->update([
            'check_out_time' => now(),
            'completed' => true,
        ]);

        return redirect()->route('training.attendance.index', $session)
            ->with('success', 'Check-out recorded.');
    }

    public function destroy($session, $attendance)
    {
        $attendance = TrainingAttendance::where('training_session_id', $session)
            ->findOrFail($attendance);

        // Decrement registered count if the farmer was registered
        $session = TrainingSession::findOrFail($session);
        if ($attendance->registration_status === 'registered') {
            $session->decrement('registered_count');
        }
        // Decrement attended count if attended
        if ($attendance->attended) {
            $session->decrement('attended_count');
        }

        $attendance->delete();

        return redirect()->route('training.attendance.index', $session)
            ->with('success', 'Attendance record removed.');
    }

    /**
     * Send registration notifications to farmer and their extension officer.
     */
    protected function sendRegistrationNotifications(TrainingSession $session, Farmer $farmer): void
    {
        $notification = new TrainingRegistrationNotification($session, $farmer);

        // Notify the farmer via their linked user account
        if ($farmer->user) {
            $farmer->user->notify($notification);
        }

        // Notify the farmer's extension officer
        if ($farmer->extensionOfficer) {
            $farmer->extensionOfficer->notify($notification);
        }
    }
}