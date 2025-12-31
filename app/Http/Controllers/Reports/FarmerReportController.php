<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Farmers\Farmer;
use App\Models\Farmers\FarmerGroup;
use App\Models\Location\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmerReportController extends Controller
{
    public function index()
    {
        $totalFarmers = Farmer::count();
        $activeFarmers = Farmer::active()->count();
        $pendingFarmers = Farmer::pending()->count();
        $organicFarmers = Farmer::organic()->count();
        $inConversionFarmers = Farmer::inConversion()->count();

        // Gender distribution
        $genderDistribution = Farmer::selectRaw('gender, COUNT(*) as count')
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        // Monthly registrations for chart (last 12 months)
        $monthlyRegistrations = Farmer::selectRaw("strftime('%Y-%m', created_at) as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Top 5 regions by farmer count
        $topRegions = Region::withCount('farmers')
            ->orderByDesc('farmers_count')
            ->take(5)
            ->get();

        // Recent farmers
        $recentFarmers = Farmer::with(['region', 'extensionOfficer'])
            ->latest()
            ->take(10)
            ->get();

        return view('reports.farmers.index', compact(
            'totalFarmers',
            'activeFarmers',
            'pendingFarmers',
            'organicFarmers',
            'inConversionFarmers',
            'genderDistribution',
            'monthlyRegistrations',
            'topRegions',
            'recentFarmers'
        ));
    }

    public function byRegion()
    {
        $regions = Region::withCount(['farmers', 'farmers as active_farmers_count' => function ($q) {
            $q->where('status', 'active');
        }, 'farmers as organic_farmers_count' => function ($q) {
            $q->where('certification_status', 'organic');
        }])
        ->withSum('farmers as total_land_size', 'total_land_size')
        ->orderByDesc('farmers_count')
        ->get();

        $totalFarmers = $regions->sum('farmers_count');

        return view('reports.farmers.by-region', compact('regions', 'totalFarmers'));
    }

    public function byStatus()
    {
        $statusCounts = Farmer::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Calculate percentages
        $total = $statusCounts->sum('count');
        $statusData = [
            'active' => ['count' => $statusCounts->get('active')->count ?? 0, 'label' => 'Active', 'color' => 'success'],
            'pending' => ['count' => $statusCounts->get('pending')->count ?? 0, 'label' => 'Pending Approval', 'color' => 'warning'],
            'inactive' => ['count' => $statusCounts->get('inactive')->count ?? 0, 'label' => 'Inactive', 'color' => 'secondary'],
            'suspended' => ['count' => $statusCounts->get('suspended')->count ?? 0, 'label' => 'Suspended', 'color' => 'danger'],
        ];

        foreach ($statusData as $key => &$data) {
            $data['percentage'] = $total > 0 ? round(($data['count'] / $total) * 100, 1) : 0;
        }

        return view('reports.farmers.by-status', compact('statusData', 'total'));
    }

    public function byCertification()
    {
        $certificationCounts = Farmer::selectRaw('certification_status, COUNT(*) as count')
            ->groupBy('certification_status')
            ->get()
            ->keyBy('certification_status');

        $total = $certificationCounts->sum('count');
        $certificationData = [
            'organic' => ['count' => $certificationCounts->get('organic')->count ?? 0, 'label' => 'Organic Certified', 'color' => 'success'],
            'in-conversion' => ['count' => $certificationCounts->get('in-conversion')->count ?? 0, 'label' => 'In Conversion', 'color' => 'warning'],
            'conventional' => ['count' => $certificationCounts->get('conventional')->count ?? 0, 'label' => 'Conventional', 'color' => 'secondary'],
        ];

        foreach ($certificationData as $key => &$data) {
            $data['percentage'] = $total > 0 ? round(($data['count'] / $total) * 100, 1) : 0;
        }

        // Certification trends (monthly for last 12 months)
        $certificationTrends = Farmer::selectRaw("strftime('%Y-%m', certification_date) as month, certification_status, COUNT(*) as count")
            ->whereNotNull('certification_date')
            ->where('certification_date', '>=', now()->subMonths(12))
            ->groupBy('month', 'certification_status')
            ->orderBy('month')
            ->get();

        return view('reports.farmers.by-certification', compact('certificationData', 'total', 'certificationTrends'));
    }

    public function registrations(Request $request)
    {
        $year = $request->get('year', now()->year);

        // Monthly registrations for selected year
        $monthlyRegistrations = Farmer::selectRaw("strftime('%m', created_at) as month, COUNT(*) as count")
            ->whereRaw("strftime('%Y', created_at) = ?", [$year])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Fill in missing months with 0
        $registrationData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = str_pad($i, 2, '0', STR_PAD_LEFT);
            $registrationData[$monthKey] = $monthlyRegistrations->get($monthKey, 0);
        }

        // Year-over-year comparison
        $yearlyTotals = Farmer::selectRaw("strftime('%Y', created_at) as year, COUNT(*) as count")
            ->groupBy('year')
            ->orderByDesc('year')
            ->take(5)
            ->pluck('count', 'year');

        // Available years for filter
        $availableYears = Farmer::selectRaw("DISTINCT strftime('%Y', created_at) as year")
            ->orderByDesc('year')
            ->pluck('year');

        return view('reports.farmers.registrations', compact('registrationData', 'yearlyTotals', 'availableYears', 'year'));
    }

    public function export(Request $request)
    {
        $farmers = Farmer::with(['region', 'district', 'village', 'extensionOfficer', 'group'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->certification, fn($q, $cert) => $q->where('certification_status', $cert))
            ->when($request->region_id, fn($q, $id) => $q->where('region_id', $id))
            ->orderBy('registration_number')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="farmers_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($farmers) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, ['FARMER REPORT']);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, ['Total Farmers: ' . $farmers->count()]);
            fputcsv($file, []);

            // CSV Header
            fputcsv($file, [
                'Registration No',
                'First Name',
                'Middle Name',
                'Last Name',
                'Gender',
                'Date of Birth',
                'Phone',
                'Email',
                'Region',
                'District',
                'Village',
                'Status',
                'Certification Status',
                'Extension Officer',
                'Farmer Group',
                'Total Land Size',
                'Household Size',
                'Registration Date',
            ]);

            foreach ($farmers as $farmer) {
                fputcsv($file, [
                    $farmer->registration_number,
                    $farmer->first_name,
                    $farmer->middle_name,
                    $farmer->last_name,
                    $farmer->gender,
                    $farmer->date_of_birth?->format('Y-m-d'),
                    $farmer->phone,
                    $farmer->email,
                    $farmer->region?->name,
                    $farmer->district?->name,
                    $farmer->village?->name,
                    $farmer->status_label,
                    $farmer->certification_status_label,
                    $farmer->extensionOfficer?->name,
                    $farmer->group?->name,
                    $farmer->total_land_size,
                    $farmer->household_size,
                    $farmer->registration_date?->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
