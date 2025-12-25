<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FarmerDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'active_farms' => 0,
            'planted_acres' => 0,
            'pending_tasks' => 0,
            'trainings_attended' => 0,
        ];

        $recentActivity = collect([]);
        $upcomingEvents = collect([]);

        return view('dashboard.farmer.index', compact('stats', 'recentActivity', 'upcomingEvents'));
    }

    public function myFarms(Request $request)
    {
        $stats = [
            'total_farms' => 0,
            'active_farms' => 0,
            'total_area' => 0,
            'organic_certified' => 0,
        ];

        $farms = collect([]); // Replace with actual farms query

        return view('dashboard.farmer.my-farms', compact('stats', 'farms'));
    }

    public function payments(Request $request)
    {
        $stats = [
            'pending' => 0,
            'received_this_year' => 0,
            'total_amount' => 0,
        ];

        $payments = collect([]); // Replace with actual payments query

        return view('dashboard.farmer.payments', compact('stats', 'payments'));
    }
}
