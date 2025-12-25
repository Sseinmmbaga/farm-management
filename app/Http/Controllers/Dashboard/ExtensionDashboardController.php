<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\Logs\ActivityLog;
use App\Models\Training\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExtensionDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Count statistics for dashboard cards
        $myFarmers = Farmer::assignedTo($user->id)->count();

        $totalFarms = Farm::whereHas('farmer', function ($q) use ($user) {
            $q->where('extension_officer_id', $user->id);
        })->count();

        $pendingRecords = Farmer::assignedTo($user->id)->pending()->count();

        $fieldVisits = ActivityLog::createdBy($user->id)
            ->whereMonth('log_date', now()->month)
            ->whereYear('log_date', now()->year)
            ->count();

        $stats = [
            'assigned_farmers' => $myFarmers,
            'active_farmers' => Farmer::assignedTo($user->id)->active()->count(),
            'pending_farmers' => $pendingRecords,
            'total_farms' => $totalFarms,
            'logs_today' => ActivityLog::createdBy($user->id)->whereDate('log_date', today())->count(),
            'logs_this_week' => ActivityLog::createdBy($user->id)
                ->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'upcoming_trainings' => TrainingSession::where('trainer_id', $user->id)
                ->where('status', 'scheduled')
                ->count(),
        ];

        $recentFarmers = Farmer::assignedTo($user->id)
            ->with(['village', 'group'])
            ->latest()
            ->take(10)
            ->get();

        $recentLogs = ActivityLog::createdBy($user->id)
            ->with(['farmer', 'farm'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.extension-officer.index', compact(
            'stats',
            'myFarmers',
            'totalFarms',
            'pendingRecords',
            'fieldVisits',
            'recentFarmers',
            'recentLogs'
        ));
    }

    public function myFarmers(Request $request)
    {
        $user = Auth::user();

        $stats = [
            'total' => Farmer::assignedTo($user->id)->count(),
            'active' => Farmer::assignedTo($user->id)->active()->count(),
            'pending' => Farmer::assignedTo($user->id)->pending()->count(),
            'inactive' => Farmer::assignedTo($user->id)->where('status', 'inactive')->count(),
        ];

        $farmers = Farmer::assignedTo($user->id)
            ->with(['village', 'district', 'region', 'group'])
            ->when($request->search, function ($q, $search) {
                $q->search($search);
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->paginate(20);

        return view('dashboard.extension-officer.my-farmers', compact('farmers', 'stats'));
    }

    public function dataCollection(Request $request)
    {
        $user = Auth::user();

        $stats = [
            'total_farmers' => Farmer::assignedTo($user->id)->active()->count(),
            'pending_records' => 0,
            'completed_today' => 0,
        ];

        $farmers = Farmer::assignedTo($user->id)
            ->active()
            ->with(['farms'])
            ->get();

        return view('dashboard.extension-officer.data-collection', compact('farmers', 'stats'));
    }
}
