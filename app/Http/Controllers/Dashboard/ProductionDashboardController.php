<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductionDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'active_plans' => 0,
            'total_harvests' => 0,
            'target_achieved' => 0,
            'pending_deliveries' => 0,
        ];

        $activePlans = collect([]);
        $recentHarvests = collect([]);

        return view('dashboard.production-manager.index', compact('stats', 'activePlans', 'recentHarvests'));
    }

    public function plans(Request $request)
    {
        $stats = [
            'draft' => 0,
            'active' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        $plans = collect([]); // Replace with actual production plans query

        return view('dashboard.production-manager.plans', compact('stats', 'plans'));
    }

    public function harvests(Request $request)
    {
        $stats = [
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'total_quantity' => 0,
        ];

        $harvests = collect([]); // Replace with actual harvests query

        return view('dashboard.production-manager.harvests', compact('stats', 'harvests'));
    }
}
