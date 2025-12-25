<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ICS\Inspection;
use App\Models\ICS\InspectionFinding;
use App\Models\Farmers\Farmer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ICSDashboardController extends Controller
{
    public function index()
    {
        // Stats
        $stats = [
            'pending_inspections' => Inspection::where('status', 'scheduled')->count(),
            'completed_this_month' => Inspection::where('status', 'completed')
                ->whereMonth('inspection_date', now()->month)
                ->whereYear('inspection_date', now()->year)
                ->count(),
            'total_findings' => InspectionFinding::where('status', 'open')->count(),
            'critical_findings' => InspectionFinding::where('status', 'open')
                ->where('severity', 'critical')
                ->count(),
            'compliance_rate' => $this->calculateComplianceRate(),
        ];

        // Upcoming inspections
        $upcomingInspections = Inspection::with(['farmer', 'farm'])
            ->where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();

        // Overdue inspections
        $overdueInspections = Inspection::with(['farmer', 'farm'])
            ->where('status', 'scheduled')
            ->where('scheduled_date', '<', now())
            ->count();

        // Recent findings
        $recentFindings = InspectionFinding::with(['inspection.farmer'])
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        // This week stats
        $thisWeekStats = [
            'inspections_completed' => Inspection::where('status', 'completed')
                ->whereBetween('inspection_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'findings_resolved' => InspectionFinding::where('status', 'resolved')
                ->whereBetween('resolved_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
        ];

        // Findings by severity
        $findingsBySeverity = [
            'critical' => InspectionFinding::where('status', 'open')->where('severity', 'critical')->count(),
            'major' => InspectionFinding::where('status', 'open')->where('severity', 'major')->count(),
            'minor' => InspectionFinding::where('status', 'open')->where('severity', 'minor')->count(),
        ];

        return view('dashboard.ics-inspector.index', compact(
            'stats',
            'upcomingInspections',
            'overdueInspections',
            'recentFindings',
            'thisWeekStats',
            'findingsBySeverity'
        ));
    }

    public function inspections(Request $request)
    {
        $query = Inspection::with(['farmer', 'farm', 'inspector']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by result
        if ($request->filled('result')) {
            $query->where('result', $request->result);
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
            $query->where(function($q) use ($search) {
                $q->where('inspection_number', 'like', "%{$search}%")
                  ->orWhereHas('farmer', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $inspections = $query->latest('scheduled_date')->paginate(15);

        // Stats
        $stats = [
            'scheduled' => Inspection::where('status', 'scheduled')->count(),
            'completed' => Inspection::where('status', 'completed')->count(),
            'passed' => Inspection::where('result', 'passed')->count(),
            'failed' => Inspection::where('result', 'failed')->count(),
        ];

        return view('dashboard.ics-inspector.inspections', compact('inspections', 'stats'));
    }

    public function findings(Request $request)
    {
        $query = InspectionFinding::with(['inspection.farmer', 'inspection.farm']);

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('inspection.farmer', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $findings = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'open' => InspectionFinding::where('status', 'open')->count(),
            'in_progress' => InspectionFinding::where('status', 'in_progress')->count(),
            'resolved' => InspectionFinding::where('status', 'resolved')->count(),
            'critical' => InspectionFinding::where('status', 'open')->where('severity', 'critical')->count(),
        ];

        return view('dashboard.ics-inspector.findings', compact('findings', 'stats'));
    }

    private function calculateComplianceRate()
    {
        $totalCompleted = Inspection::where('status', 'completed')->count();
        if ($totalCompleted === 0) return 0;

        $passed = Inspection::where('status', 'completed')->where('result', 'passed')->count();
        return round(($passed / $totalCompleted) * 100, 1);
    }
}
