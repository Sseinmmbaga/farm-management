<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Training\TrainingProgram;
use App\Models\Training\TrainingSession;
use App\Models\Training\TrainingAttendance;
use App\Models\Training\TrainingCertificate;
use Carbon\Carbon;

class TrainingDashboardController extends Controller
{
    public function index()
    {
        // Main dashboard statistics
        $totalPrograms = TrainingProgram::count();
        $upcomingSessions = TrainingSession::upcoming()->count();
        $trainedFarmers = TrainingAttendance::where('attended', true)->distinct('farmer_id')->count('farmer_id');
        $certificatesIssued = TrainingCertificate::count();

        // Additional stats for compatibility
        $stats = [
            'scheduled_trainings' => TrainingSession::scheduled()->count(),
            'completed_this_month' => TrainingSession::completed()
                ->whereMonth('scheduled_date', now()->month)
                ->whereYear('scheduled_date', now()->year)
                ->count(),
            'total_participants' => TrainingSession::sum('registered_count'),
            'avg_attendance' => TrainingSession::where('registered_count', '>', 0)
                ->avg('attended_count'),
        ];

        $upcomingTrainings = TrainingSession::upcoming()
            ->with('program')
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        $recentTrainings = TrainingSession::completed()
            ->with('program')
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.training-coordinator.index', compact(
            'totalPrograms',
            'upcomingSessions',
            'trainedFarmers',
            'certificatesIssued',
            'stats',
            'upcomingTrainings',
            'recentTrainings'
        ));
    }

    public function trainings(Request $request)
    {
        // Statistics
        $stats = [
            'scheduled' => TrainingSession::scheduled()->count(),
            'in_progress' => TrainingSession::where('status', 'in_progress')->count(),
            'completed' => TrainingSession::completed()->count(),
            'cancelled' => TrainingSession::cancelled()->count(),
        ];

        // Build query with filters
        $query = TrainingSession::with(['program', 'trainer']);

        // Status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'scheduled') {
                $query->scheduled();
            } elseif ($status === 'in_progress') {
                $query->where('status', 'in_progress');
            } elseif ($status === 'completed') {
                $query->completed();
            } elseif ($status === 'cancelled') {
                $query->cancelled();
            }
        }

        // Category filter (via program category)
        if ($request->filled('category')) {
            $query->whereHas('program', function ($q) use ($request) {
                $q->where('category', $request->input('category'));
            });
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('scheduled_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('scheduled_date', '<=', $request->input('to_date'));
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhereHas('program', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('name_sw', 'like', "%{$search}%");
                  });
            });
        }

        // Order and paginate
        $trainings = $query->orderBy('scheduled_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.training-coordinator.trainings', compact('stats', 'trainings'));
    }

    public function calendar(Request $request)
    {
        // Determine month from request or default to current month
        $month = $request->input('month', now()->format('Y-m'));
        try {
            $currentMonth = Carbon::parse($month)->startOfMonth();
        } catch (\Exception $e) {
            $currentMonth = now()->startOfMonth();
        }

        $start = $currentMonth->copy()->startOfMonth();
        $end = $currentMonth->copy()->endOfMonth();

        // Fetch training sessions for the month
        $events = TrainingSession::whereBetween('scheduled_date', [$start, $end])
            ->with('program')
            ->orderBy('scheduled_date')
            ->get();

        return view('dashboard.training-coordinator.calendar', compact('events', 'currentMonth'));
    }
}
