<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Logs\HarvestLog;
use App\Models\Logs\ActivityLog;
use App\Models\Farms\Season;
use App\Models\Location\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YieldReportController extends Controller
{
    public function index()
    {
        // Total harvested quantity
        $totalQuantity = HarvestLog::sum('quantity_harvested');
        $totalArea = HarvestLog::sum('area_harvested');
        $averageYield = $totalArea > 0 ? $totalQuantity / $totalArea : 0;

        // Recent harvests (last 10)
        $recentHarvests = HarvestLog::with('activityLog.farmer', 'activityLog.season')
            ->latest()
            ->take(10)
            ->get();

        // Top crops by quantity
        $topCrops = HarvestLog::selectRaw('crop_type, SUM(quantity_harvested) as total_quantity, AVG(yield_per_hectare) as avg_yield')
            ->groupBy('crop_type')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Monthly harvest trend (last 12 months)
        $monthlyTrend = ActivityLog::join('harvest_logs', 'activity_logs.id', '=', 'harvest_logs.activity_log_id')
            ->selectRaw("strftime('%Y-%m', activity_logs.log_date) as month, SUM(harvest_logs.quantity_harvested) as total_quantity")
            ->where('activity_logs.log_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_quantity', 'month');

        // Fill missing months
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $monthlyData[$monthKey] = $monthlyTrend->get($monthKey, 0);
        }

        return view('reports.yields.index', compact(
            'totalQuantity',
            'totalArea',
            'averageYield',
            'recentHarvests',
            'topCrops',
            'monthlyData'
        ));
    }

    public function bySeason()
    {
        $seasonYields = Season::withCount(['activityLogs as harvest_count' => function ($query) {
                $query->where('type', 'harvest');
            }])
            ->withSum(['activityLogs.harvestLog as total_quantity' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity_harvested), 0)'));
            }], 'quantity_harvested')
            ->withAvg(['activityLogs.harvestLog as avg_yield' => function ($query) {
                $query->select(DB::raw('COALESCE(AVG(yield_per_hectare), 0)'));
            }], 'yield_per_hectare')
            ->orderByDesc('total_quantity')
            ->get();

        $totalQuantity = $seasonYields->sum('total_quantity');

        return view('reports.yields.by-season', compact('seasonYields', 'totalQuantity'));
    }

    public function byCrop()
    {
        $cropYields = HarvestLog::selectRaw('crop_type,
                COUNT(*) as harvest_count,
                SUM(quantity_harvested) as total_quantity,
                AVG(yield_per_hectare) as avg_yield,
                MIN(yield_per_hectare) as min_yield,
                MAX(yield_per_hectare) as max_yield')
            ->groupBy('crop_type')
            ->orderByDesc('total_quantity')
            ->get();

        $totalQuantity = $cropYields->sum('total_quantity');

        // Quality grade distribution per crop
        $qualityDistribution = HarvestLog::selectRaw('crop_type, quality_grade, COUNT(*) as count')
            ->whereNotNull('quality_grade')
            ->groupBy('crop_type', 'quality_grade')
            ->get()
            ->groupBy('crop_type');

        return view('reports.yields.by-crop', compact('cropYields', 'totalQuantity', 'qualityDistribution'));
    }

    public function byRegion()
    {
        $regionYields = Region::withCount(['activityLogs as harvest_count' => function ($query) {
                $query->where('type', 'harvest');
            }])
            ->withSum(['activityLogs.harvestLog as total_quantity' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity_harvested), 0)'));
            }], 'quantity_harvested')
            ->withAvg(['activityLogs.harvestLog as avg_yield' => function ($query) {
                $query->select(DB::raw('COALESCE(AVG(yield_per_hectare), 0)'));
            }], 'yield_per_hectare')
            ->orderByDesc('total_quantity')
            ->get();

        $totalQuantity = $regionYields->sum('total_quantity');

        return view('reports.yields.by-region', compact('regionYields', 'totalQuantity'));
    }

    public function comparison(Request $request)
    {
        $groupBy = $request->get('group_by', 'season');
        $compareWith = $request->get('compare_with', 'crop');

        // Build comparison data based on selected dimensions
        if ($groupBy === 'season' && $compareWith === 'crop') {
            $comparisonData = DB::table('harvest_logs')
                ->join('activity_logs', 'harvest_logs.activity_log_id', '=', 'activity_logs.id')
                ->join('seasons', 'activity_logs.season_id', '=', 'seasons.id')
                ->selectRaw('seasons.name as season_name, harvest_logs.crop_type,
                    SUM(harvest_logs.quantity_harvested) as total_quantity,
                    AVG(harvest_logs.yield_per_hectare) as avg_yield')
                ->groupBy('seasons.name', 'harvest_logs.crop_type')
                ->orderBy('seasons.start_date')
                ->orderByDesc('total_quantity')
                ->get()
                ->groupBy('season_name');
        } elseif ($groupBy === 'region' && $compareWith === 'crop') {
            $comparisonData = DB::table('harvest_logs')
                ->join('activity_logs', 'harvest_logs.activity_log_id', '=', 'activity_logs.id')
                ->join('regions', 'activity_logs.region_id', '=', 'regions.id')
                ->selectRaw('regions.name as region_name, harvest_logs.crop_type,
                    SUM(harvest_logs.quantity_harvested) as total_quantity,
                    AVG(harvest_logs.yield_per_hectare) as avg_yield')
                ->groupBy('regions.name', 'harvest_logs.crop_type')
                ->orderBy('regions.name')
                ->orderByDesc('total_quantity')
                ->get()
                ->groupBy('region_name');
        } else {
            // Default: season vs region
            $comparisonData = DB::table('harvest_logs')
                ->join('activity_logs', 'harvest_logs.activity_log_id', '=', 'activity_logs.id')
                ->join('seasons', 'activity_logs.season_id', '=', 'seasons.id')
                ->join('regions', 'activity_logs.region_id', '=', 'regions.id')
                ->selectRaw('seasons.name as season_name, regions.name as region_name,
                    SUM(harvest_logs.quantity_harvested) as total_quantity,
                    AVG(harvest_logs.yield_per_hectare) as avg_yield')
                ->groupBy('seasons.name', 'regions.name')
                ->orderBy('seasons.start_date')
                ->orderBy('regions.name')
                ->get()
                ->groupBy('season_name');
        }

        return view('reports.yields.comparison', compact('comparisonData', 'groupBy', 'compareWith'));
    }

    public function export(Request $request)
    {
        $harvests = HarvestLog::with(['activityLog.farmer', 'activityLog.season', 'activityLog.region'])
            ->when($request->season_id, fn($q, $id) => $q->whereHas('activityLog', fn($q) => $q->where('season_id', $id)))
            ->when($request->crop_type, fn($q, $crop) => $q->where('crop_type', $crop))
            ->when($request->region_id, fn($q, $id) => $q->whereHas('activityLog', fn($q) => $q->where('region_id', $id)))
            ->orderByDesc('created_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="yield_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($harvests) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, ['YIELD REPORT']);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, ['Total Records: ' . $harvests->count()]);
            fputcsv($file, []);

            // CSV Header
            fputcsv($file, [
                'Crop Type',
                'Variety',
                'Harvest Date',
                'Farmer',
                'Season',
                'Region',
                'Area Harvested',
                'Quantity Harvested',
                'Yield per Hectare',
                'Quality Grade',
                'Moisture Content',
                'Harvest Method',
                'Sale Price',
                'Total Value',
            ]);

            foreach ($harvests as $harvest) {
                fputcsv($file, [
                    $harvest->crop_type_label,
                    $harvest->variety,
                    $harvest->activityLog->log_date?->format('Y-m-d'),
                    $harvest->activityLog->farmer?->full_name,
                    $harvest->activityLog->season?->name,
                    $harvest->activityLog->region?->name,
                    $harvest->area_display,
                    $harvest->quantity_display,
                    $harvest->yield_display,
                    $harvest->quality_grade_label,
                    $harvest->moisture_content ? $harvest->moisture_content . '%' : '',
                    $harvest->harvest_method_label,
                    $harvest->sale_price ? number_format($harvest->sale_price, 2) . ' ' . $harvest->price_unit : '',
                    $harvest->total_value ? number_format($harvest->total_value, 2) : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}