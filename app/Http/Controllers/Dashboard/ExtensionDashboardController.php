<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\Farms\FarmVisit;
use App\Models\ICS\FarmerCertification;
use App\Models\Logs\ActivityLog;
use App\Models\Tasks\Task;
use App\Models\Training\TrainingAttendance;
use App\Models\Training\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Performance Metrics
        $performanceMetrics = $this->getPerformanceMetrics($user);

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

        // My Tasks
        $myTasks = Task::assignedToUser($user->id)
            ->active()
            ->orderBy('planned_end_date')
            ->take(5)
            ->get();

        // Upcoming Visits
        $upcomingVisits = FarmVisit::where('supervisor_id', $user->id)
            ->scheduled()
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();

        return view('dashboard.extension-officer.index', compact(
            'stats',
            'myFarmers',
            'totalFarms',
            'pendingRecords',
            'fieldVisits',
            'recentFarmers',
            'recentLogs',
            'performanceMetrics',
            'myTasks',
            'upcomingVisits'
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

    /**
     * Get performance metrics for the extension officer
     */
    private function getPerformanceMetrics($user): array
    {
        $farmerIds = Farmer::assignedTo($user->id)->pluck('id');

        // Visit completion rate (this month)
        $totalScheduledVisits = FarmVisit::where('supervisor_id', $user->id)
            ->whereMonth('scheduled_date', now()->month)
            ->whereYear('scheduled_date', now()->year)
            ->count();

        $completedVisits = FarmVisit::where('supervisor_id', $user->id)
            ->completed()
            ->whereMonth('actual_date', now()->month)
            ->whereYear('actual_date', now()->year)
            ->count();

        $visitCompletionRate = $totalScheduledVisits > 0
            ? round(($completedVisits / $totalScheduledVisits) * 100, 1)
            : 0;

        // Task completion rate
        $totalTasks = Task::assignedToUser($user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $completedTasks = Task::assignedToUser($user->id)
            ->completed()
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->count();

        $taskCompletionRate = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100, 1)
            : 0;

        // Farmer certification status
        $organicFarmers = Farmer::assignedTo($user->id)->organic()->count();
        $inConversionFarmers = Farmer::assignedTo($user->id)->inConversion()->count();
        $totalAssignedFarmers = Farmer::assignedTo($user->id)->count();

        $certificationRate = $totalAssignedFarmers > 0
            ? round(($organicFarmers / $totalAssignedFarmers) * 100, 1)
            : 0;

        // Training participation (farmers attended trainings this year)
        $farmersWithTraining = TrainingAttendance::whereIn('farmer_id', $farmerIds)
            ->attended()
            ->whereYear('created_at', now()->year)
            ->distinct('farmer_id')
            ->count('farmer_id');

        $trainingParticipationRate = $totalAssignedFarmers > 0
            ? round(($farmersWithTraining / $totalAssignedFarmers) * 100, 1)
            : 0;

        // Activity logs this month vs target (e.g., 50 logs per month target)
        $monthlyTarget = 50;
        $logsThisMonth = ActivityLog::createdBy($user->id)
            ->whereMonth('log_date', now()->month)
            ->whereYear('log_date', now()->year)
            ->count();

        $activityProgress = min(round(($logsThisMonth / $monthlyTarget) * 100, 1), 100);

        // Trend data for last 6 months
        $monthlyVisits = $this->getMonthlyVisitTrend($user->id);
        $monthlyLogs = $this->getMonthlyActivityTrend($user->id);

        return [
            'visit_completion_rate' => $visitCompletionRate,
            'completed_visits' => $completedVisits,
            'total_scheduled_visits' => $totalScheduledVisits,
            'task_completion_rate' => $taskCompletionRate,
            'completed_tasks' => $completedTasks,
            'total_tasks' => $totalTasks,
            'certification_rate' => $certificationRate,
            'organic_farmers' => $organicFarmers,
            'in_conversion_farmers' => $inConversionFarmers,
            'training_participation_rate' => $trainingParticipationRate,
            'farmers_with_training' => $farmersWithTraining,
            'activity_progress' => $activityProgress,
            'logs_this_month' => $logsThisMonth,
            'monthly_target' => $monthlyTarget,
            'monthly_visits' => $monthlyVisits,
            'monthly_logs' => $monthlyLogs,
        ];
    }

    /**
     * Get monthly visit trend for the last 6 months
     */
    private function getMonthlyVisitTrend($userId): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = FarmVisit::where('supervisor_id', $userId)
                ->completed()
                ->whereMonth('actual_date', $month->month)
                ->whereYear('actual_date', $month->year)
                ->count();

            $data[] = [
                'month' => $month->format('M'),
                'count' => $count,
            ];
        }
        return $data;
    }

    /**
     * Get monthly activity log trend for the last 6 months
     */
    private function getMonthlyActivityTrend($userId): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = ActivityLog::createdBy($userId)
                ->whereMonth('log_date', $month->month)
                ->whereYear('log_date', $month->year)
                ->count();

            $data[] = [
                'month' => $month->format('M'),
                'count' => $count,
            ];
        }
        return $data;
    }

    /**
     * Performance metrics page
     */
    public function performance()
    {
        $user = Auth::user();
        $performanceMetrics = $this->getPerformanceMetrics($user);

        // Extended metrics
        $farmerIds = Farmer::assignedTo($user->id)->pluck('id');

        // Farmer status breakdown
        $farmerStatusBreakdown = [
            'active' => Farmer::assignedTo($user->id)->active()->count(),
            'pending' => Farmer::assignedTo($user->id)->pending()->count(),
            'inactive' => Farmer::assignedTo($user->id)->where('status', 'inactive')->count(),
            'suspended' => Farmer::assignedTo($user->id)->where('status', 'suspended')->count(),
        ];

        // Certification breakdown
        $certificationBreakdown = [
            'organic' => Farmer::assignedTo($user->id)->organic()->count(),
            'in_conversion' => Farmer::assignedTo($user->id)->inConversion()->count(),
            'conventional' => Farmer::assignedTo($user->id)->where('certification_status', 'conventional')->count(),
        ];

        // Task breakdown by status
        $taskBreakdown = [
            'pending' => Task::assignedToUser($user->id)->pending()->count(),
            'in_progress' => Task::assignedToUser($user->id)->inProgress()->count(),
            'completed' => Task::assignedToUser($user->id)->completed()->count(),
            'overdue' => Task::assignedToUser($user->id)->overdue()->count(),
        ];

        return view('dashboard.extension-officer.performance', compact(
            'performanceMetrics',
            'farmerStatusBreakdown',
            'certificationBreakdown',
            'taskBreakdown'
        ));
    }
}
