<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrainingDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'scheduled_trainings' => 0,
            'completed_this_month' => 0,
            'total_participants' => 0,
            'avg_attendance' => 0,
        ];

        $upcomingTrainings = collect([]);
        $recentTrainings = collect([]);

        return view('dashboard.training-coordinator.index', compact('stats', 'upcomingTrainings', 'recentTrainings'));
    }

    public function trainings(Request $request)
    {
        $stats = [
            'scheduled' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        $trainings = collect([]); // Replace with actual trainings query

        return view('dashboard.training-coordinator.trainings', compact('stats', 'trainings'));
    }

    public function calendar(Request $request)
    {
        $events = collect([]); // Replace with actual calendar events query

        return view('dashboard.training-coordinator.calendar', compact('events'));
    }
}
