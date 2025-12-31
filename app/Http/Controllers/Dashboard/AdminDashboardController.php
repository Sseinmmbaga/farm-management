<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Farmers\Farmer;
use App\Models\Farms\Farm;
use App\Models\Stock\StockItem;
use App\Models\ICS\Inspection;
use App\Models\Training\TrainingSession;
use App\Models\User;
use App\Models\Logs\ActivityLog;
use App\Models\Location\Region;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Core Statistics
        $stats = [
            'total_farmers' => Farmer::count(),
            'active_farmers' => Farmer::active()->count(),
            'pending_farmers' => Farmer::pending()->count(),
            'total_farms' => Farm::count(),
            'organic_farms' => Farm::organic()->count(),
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'low_stock_items' => StockItem::lowStock()->count(),
            'pending_inspections' => Inspection::where('status', 'scheduled')->count(),
            'upcoming_trainings' => TrainingSession::where('status', 'scheduled')
                ->where('scheduled_date', '>=', now())
                ->count(),
        ];

        // Recent Activity
        $recentFarmers = Farmer::with(['village', 'extensionOfficer'])
            ->latest()
            ->take(5)
            ->get();

        $recentInspections = Inspection::with(['farmer', 'inspector'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Activity Logs
        $recentActivityLogs = ActivityLog::with(['farmer', 'farm'])
            ->latest()
            ->take(10)
            ->get();

        // Certification Statistics
        $certificationStats = [
            'organic_farmers' => Farmer::organic()->count(),
            'in_conversion_farmers' => Farmer::inConversion()->count(),
            'conventional_farmers' => Farmer::count() - Farmer::organic()->count() - Farmer::inConversion()->count(),
        ];

        // Extension Officer Statistics
        $extensionOfficers = User::extensionOfficers()->active()->count();
        $farmersPerOfficer = $extensionOfficers > 0 ? round($stats['total_farmers'] / $extensionOfficers, 1) : 0;
        
        // Area Statistics
        $totalCultivatedArea = Farm::sum('cultivated_area');
        $organicConversionRate = $stats['total_farms'] > 0 ?
            round(($stats['organic_farms'] / $stats['total_farms']) * 100, 1) : 0;

        // Regional Distribution (top 5 regions by farmer count)
        $farmersByRegion = Region::withCount('farmers')
            ->orderByDesc('farmers_count')
            ->take(5)
            ->get();

        // Upcoming Inspections (next 7 days)
        $upcomingInspections = Inspection::with(['farmer', 'inspector'])
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_date', [now(), now()->addDays(7)])
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();

        // Upcoming Trainings (next 14 days)
        $upcomingTrainings = TrainingSession::where('status', 'scheduled')
            ->whereBetween('scheduled_date', [now(), now()->addDays(14)])
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();

        // Alerts - items needing attention
        $alerts = [];

        // Overdue inspections
        $overdueInspections = Inspection::where('status', 'scheduled')
            ->where('scheduled_date', '<', now())
            ->count();
        if ($overdueInspections > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fas fa-exclamation-circle',
                'message' => "{$overdueInspections} overdue inspection(s) need attention",
                'link' => route('inspections.index') . '?status=overdue',
            ];
        }

        // Low stock items
        if ($stats['low_stock_items'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-boxes',
                'message' => "{$stats['low_stock_items']} item(s) are running low on stock",
                'link' => route('stock.index') . '?filter=low_stock',
            ];
        }

        // Pending farmer approvals
        if ($stats['pending_farmers'] > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-user-clock',
                'message' => "{$stats['pending_farmers']} farmer(s) awaiting approval",
                'link' => route('farmers.index') . '?status=pending',
            ];
        }

        // This week's statistics
        $thisWeekStats = [
            'new_farmers' => Farmer::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'new_farms' => Farm::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'completed_inspections' => Inspection::where('status', 'completed')
                ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'logs_recorded' => ActivityLog::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
        ];

        // Chart data
        $monthlyRegistrations = Farmer::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        $stockByCategory = \App\Models\Stock\StockItem::with('category')
            ->selectRaw('category_id, COUNT(*) as count, SUM(current_quantity) as total_quantity')
            ->groupBy('category_id')
            ->get();

        return view('dashboard.admin.index', compact(
            'stats',
            'recentFarmers',
            'recentInspections',
            'recentActivityLogs',
            'certificationStats',
            'extensionOfficers',
            'farmersPerOfficer',
            'totalCultivatedArea',
            'organicConversionRate',
            'farmersByRegion',
            'upcomingInspections',
            'upcomingTrainings',
            'alerts',
            'thisWeekStats',
            'monthlyRegistrations',
            'stockByCategory'
        ));
    }

    public function statistics()
    {
        // Farmers by region
        $farmersByRegion = Farmer::selectRaw('region_id, COUNT(*) as count')
            ->groupBy('region_id')
            ->with('region')
            ->get();

        // Farmers by certification status
        $farmersByCertification = Farmer::selectRaw('certification_status, COUNT(*) as count')
            ->groupBy('certification_status')
            ->get();

        // Monthly registrations (using strftime for SQLite compatibility)
        $monthlyRegistrations = Farmer::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('dashboard.admin.statistics', compact(
            'farmersByRegion',
            'farmersByCertification',
            'monthlyRegistrations'
        ));
    }
}
