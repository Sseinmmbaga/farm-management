<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\ICS\Inspection;
use App\Models\ICS\InspectionFinding;
use App\Models\ICS\FarmerCertification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplianceReportController extends Controller
{
    public function index()
    {
        // Total inspections
        $totalInspections = Inspection::count();
        $completedInspections = Inspection::completed()->count();
        $passedInspections = Inspection::passed()->count();
        $failedInspections = Inspection::failed()->count();
        $passRate = $totalInspections > 0 ? round(($passedInspections / $totalInspections) * 100, 1) : 0;

        // Total certifications
        $totalCertifications = FarmerCertification::count();
        $activeCertifications = FarmerCertification::active()->count();
        $inConversionCertifications = FarmerCertification::inConversion()->count();
        $expiringSoonCertifications = FarmerCertification::expiringSoon(30)->count();

        // Total findings
        $totalFindings = InspectionFinding::count();
        $criticalFindings = InspectionFinding::where('severity', 'critical')->count();
        $nonCompliantFindings = InspectionFinding::where('compliance_status', 'non-compliant')->count();

        // Monthly inspections trend (last 12 months)
        $monthlyInspections = Inspection::selectRaw("strftime('%Y-%m', inspection_date) as month, COUNT(*) as count")
            ->whereNotNull('inspection_date')
            ->where('inspection_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Fill missing months
        $inspectionTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $inspectionTrend[$monthKey] = $monthlyInspections->get($monthKey, 0);
        }

        // Recent inspections
        $recentInspections = Inspection::with(['farmer', 'checklist'])
            ->latest()
            ->take(10)
            ->get();

        return view('reports.compliance.index', compact(
            'totalInspections',
            'completedInspections',
            'passedInspections',
            'failedInspections',
            'passRate',
            'totalCertifications',
            'activeCertifications',
            'inConversionCertifications',
            'expiringSoonCertifications',
            'totalFindings',
            'criticalFindings',
            'nonCompliantFindings',
            'inspectionTrend',
            'recentInspections'
        ));
    }

    public function inspections(Request $request)
    {
        $inspections = Inspection::with(['farmer', 'checklist', 'inspector'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->result, fn($q, $result) => $q->where('result', $result))
            ->when($request->checklist_id, fn($q, $id) => $q->where('inspection_checklist_id', $id))
            ->when($request->date_from, fn($q, $date) => $q->where('inspection_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->where('inspection_date', '<=', $date))
            ->orderByDesc('inspection_date')
            ->paginate(25)
            ->withQueryString();

        // Statistics for filters
        $statusCounts = Inspection::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $resultCounts = Inspection::selectRaw('result, COUNT(*) as count')
            ->groupBy('result')
            ->pluck('count', 'result');

        return view('reports.compliance.inspections', compact('inspections', 'statusCounts', 'resultCounts'));
    }

    public function findings(Request $request)
    {
        $findings = InspectionFinding::with(['inspection.farmer', 'inspection.checklist'])
            ->when($request->severity, fn($q, $severity) => $q->where('severity', $severity))
            ->when($request->compliance_status, fn($q, $status) => $q->where('compliance_status', $status))
            ->when($request->date_from, fn($q, $date) => $q->whereHas('inspection', fn($q) => $q->where('inspection_date', '>=', $date)))
            ->when($request->date_to, fn($q, $date) => $q->whereHas('inspection', fn($q) => $q->where('inspection_date', '<=', $date)))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        // Severity distribution
        $severityDistribution = InspectionFinding::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->pluck('count', 'severity');

        // Compliance status distribution
        $complianceDistribution = InspectionFinding::selectRaw('compliance_status, COUNT(*) as count')
            ->groupBy('compliance_status')
            ->pluck('count', 'compliance_status');

        // Top non‑compliant categories
        $topCategories = InspectionFinding::selectRaw('category, COUNT(*) as count')
            ->where('compliance_status', 'non-compliant')
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        return view('reports.compliance.findings', compact(
            'findings',
            'severityDistribution',
            'complianceDistribution',
            'topCategories'
        ));
    }

    public function certifications(Request $request)
    {
        $certifications = FarmerCertification::with(['farmer', 'approvedBy', 'lastInspection'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->standard_id, fn($q, $id) => $q->where('compliance_standard_id', $id))
            ->when($request->expiring_soon, fn($q) => $q->expiringSoon(30))
            ->orderByDesc('certification_date')
            ->paginate(25)
            ->withQueryString();

        // Status counts
        $statusCounts = FarmerCertification::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Certification trends (monthly)
        $monthlyCertifications = FarmerCertification::selectRaw("strftime('%Y-%m', certification_date) as month, status, COUNT(*) as count")
            ->whereNotNull('certification_date')
            ->where('certification_date', '>=', now()->subMonths(12))
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        // Expiration timeline
        $expirationTimeline = FarmerCertification::selectRaw("strftime('%Y-%m', expiry_date) as month, COUNT(*) as count")
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return view('reports.compliance.certifications', compact(
            'certifications',
            'statusCounts',
            'monthlyCertifications',
            'expirationTimeline'
        ));
    }

    public function export(Request $request)
    {
        $inspections = Inspection::with(['farmer', 'checklist', 'inspector', 'findings'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->result, fn($q, $result) => $q->where('result', $result))
            ->when($request->date_from, fn($q, $date) => $q->where('inspection_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->where('inspection_date', '<=', $date))
            ->orderByDesc('inspection_date')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="compliance_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($inspections) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, ['COMPLIANCE INSPECTION REPORT']);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, ['Total Inspections: ' . $inspections->count()]);
            fputcsv($file, []);

            // CSV Header
            fputcsv($file, [
                'Inspection Number',
                'Farmer',
                'Farm',
                'Checklist',
                'Inspection Date',
                'Inspector',
                'Status',
                'Result',
                'Total Score',
                'Max Score',
                'Percentage Score',
                'Items Checked',
                'Items Compliant',
                'Items Non‑Compliant',
                'Critical Failures',
                'Requires Follow‑Up',
                'Follow‑Up Date',
                'Summary',
            ]);

            foreach ($inspections as $inspection) {
                fputcsv($file, [
                    $inspection->inspection_number,
                    $inspection->farmer?->full_name,
                    $inspection->farm?->name,
                    $inspection->checklist?->name,
                    $inspection->inspection_date?->format('Y-m-d'),
                    $inspection->inspector?->name,
                    $inspection->status_label,
                    $inspection->result_label,
                    $inspection->total_score,
                    $inspection->max_possible_score,
                    $inspection->percentage_score . '%',
                    $inspection->items_checked,
                    $inspection->items_compliant,
                    $inspection->items_non_compliant,
                    $inspection->critical_failures,
                    $inspection->requires_follow_up ? 'Yes' : 'No',
                    $inspection->follow_up_date?->format('Y-m-d'),
                    $inspection->summary,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}