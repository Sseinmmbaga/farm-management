<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Stock\StockItem;
use App\Models\Stock\StockCategory;
use App\Models\Stock\StockTransaction;
use App\Models\Stock\StockDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    public function index()
    {
        // Total stock items
        $totalItems = StockItem::count();
        $totalValue = StockItem::sum(DB::raw('quantity_on_hand * unit_cost'));
        $lowStockCount = StockItem::lowStock()->count();
        $criticalStockCount = StockItem::criticalStock()->count();
        $outOfStockCount = StockItem::outOfStock()->count();

        // Recent transactions (last 10)
        $recentTransactions = StockTransaction::with(['item', 'batch'])
            ->latest()
            ->take(10)
            ->get();

        // Stock by category (value)
        $stockByCategory = StockCategory::withSum(['items as total_quantity' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity_on_hand), 0)'));
            }], 'quantity_on_hand')
            ->withSum(['items as total_value' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity_on_hand * unit_cost), 0)'));
            }], DB::raw('quantity_on_hand * unit_cost'))
            ->orderByDesc('total_value')
            ->get();

        // Monthly stock intake trend (last 12 months)
        $monthlyIntake = StockTransaction::where('transaction_type', 'intake')
            ->selectRaw("strftime('%Y-%m', transaction_date) as month, SUM(quantity) as total_quantity")
            ->where('transaction_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_quantity', 'month');

        // Fill missing months
        $intakeTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $intakeTrend[$monthKey] = $monthlyIntake->get($monthKey, 0);
        }

        return view('reports.stock.index', compact(
            'totalItems',
            'totalValue',
            'lowStockCount',
            'criticalStockCount',
            'outOfStockCount',
            'recentTransactions',
            'stockByCategory',
            'intakeTrend'
        ));
    }

    public function inventory(Request $request)
    {
        $items = StockItem::with(['category'])
            ->when($request->category_id, fn($q, $id) => $q->where('category_id', $id))
            ->when($request->status, function ($q, $status) {
                if ($status === 'low') return $q->lowStock();
                if ($status === 'critical') return $q->criticalStock();
                if ($status === 'out') return $q->outOfStock();
                if ($status === 'in') return $q->inStock();
            })
            ->when($request->organic, fn($q, $organic) => $q->where('is_organic_approved', $organic))
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $categories = StockCategory::orderBy('name')->get();

        // Summary stats
        $totalQuantity = $items->sum('quantity_on_hand');
        $totalValue = $items->sum('stock_value');

        return view('reports.stock.inventory', compact('items', 'categories', 'totalQuantity', 'totalValue'));
    }

    public function movements(Request $request)
    {
        $movements = StockTransaction::with(['item', 'batch', 'user'])
            ->when($request->type, fn($q, $type) => $q->where('transaction_type', $type))
            ->when($request->item_id, fn($q, $id) => $q->where('stock_item_id', $id))
            ->when($request->date_from, fn($q, $date) => $q->where('transaction_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->where('transaction_date', '<=', $date))
            ->orderByDesc('transaction_date')
            ->paginate(25)
            ->withQueryString();

        // Summary by type
        $summaryByType = StockTransaction::selectRaw('transaction_type, COUNT(*) as count, SUM(quantity) as total_quantity')
            ->groupBy('transaction_type')
            ->pluck('total_quantity', 'transaction_type');

        // Monthly movement summary
        $monthlySummary = StockTransaction::selectRaw("strftime('%Y-%m', transaction_date) as month, transaction_type, SUM(quantity) as total_quantity")
            ->where('transaction_date', '>=', now()->subMonths(12))
            ->groupBy('month', 'transaction_type')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        return view('reports.stock.movements', compact('movements', 'summaryByType', 'monthlySummary'));
    }

    public function distributions(Request $request)
    {
        $distributions = StockDistribution::with(['item', 'farmer', 'request'])
            ->when($request->item_id, fn($q, $id) => $q->where('stock_item_id', $id))
            ->when($request->farmer_id, fn($q, $id) => $q->where('farmer_id', $id))
            ->when($request->date_from, fn($q, $date) => $q->where('distribution_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->where('distribution_date', '<=', $date))
            ->orderByDesc('distribution_date')
            ->paginate(25)
            ->withQueryString();

        // Summary by item
        $summaryByItem = StockDistribution::selectRaw('stock_item_id, COUNT(*) as count, SUM(quantity) as total_quantity')
            ->with('item')
            ->groupBy('stock_item_id')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        // Monthly distribution trend
        $monthlyDistribution = StockDistribution::selectRaw("strftime('%Y-%m', distribution_date) as month, SUM(quantity) as total_quantity")
            ->where('distribution_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_quantity', 'month');

        return view('reports.stock.distributions', compact('distributions', 'summaryByItem', 'monthlyDistribution'));
    }

    public function valuation(Request $request)
    {
        // Valuation by category
        $valuationByCategory = StockCategory::withSum(['items as total_quantity' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity_on_hand), 0)'));
            }], 'quantity_on_hand')
            ->withSum(['items as total_value' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity_on_hand * unit_cost), 0)'));
            }], DB::raw('quantity_on_hand * unit_cost'))
            ->orderByDesc('total_value')
            ->get();

        // Valuation by organic status
        $valuationByOrganic = StockItem::selectRaw('is_organic_approved, COUNT(*) as item_count, SUM(quantity_on_hand) as total_quantity, SUM(quantity_on_hand * unit_cost) as total_value')
            ->groupBy('is_organic_approved')
            ->get();

        // Top 10 highest value items
        $topValueItems = StockItem::with('category')
            ->selectRaw('*, (quantity_on_hand * unit_cost) as item_value')
            ->orderByDesc('item_value')
            ->take(10)
            ->get();

        // Total valuation
        $totalValuation = $valuationByCategory->sum('total_value');

        // Aging inventory (items with no movement in last 6 months)
        $agingItems = StockItem::whereDoesntHave('transactions', function ($q) {
                $q->where('transaction_date', '>=', now()->subMonths(6));
            })
            ->where('quantity_on_hand', '>', 0)
            ->with('category')
            ->orderByDesc('quantity_on_hand')
            ->take(10)
            ->get();

        return view('reports.stock.valuation', compact(
            'valuationByCategory',
            'valuationByOrganic',
            'topValueItems',
            'totalValuation',
            'agingItems'
        ));
    }

    public function export(Request $request)
    {
        $items = StockItem::with(['category'])
            ->when($request->category_id, fn($q, $id) => $q->where('category_id', $id))
            ->when($request->status, function ($q, $status) {
                if ($status === 'low') return $q->lowStock();
                if ($status === 'critical') return $q->criticalStock();
                if ($status === 'out') return $q->outOfStock();
            })
            ->orderBy('name')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, ['STOCK INVENTORY REPORT']);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, ['Total Items: ' . $items->count()]);
            fputcsv($file, []);

            // CSV Header
            fputcsv($file, [
                'Item Code',
                'Item Name',
                'Category',
                'Unit',
                'Quantity on Hand',
                'Quantity Available',
                'Reorder Level',
                'Reorder Quantity',
                'Unit Cost',
                'Unit Price',
                'Stock Value',
                'Organic Approved',
                'Stock Status',
                'Warehouse Location',
                'Bin Location',
            ]);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->code,
                    $item->display_name,
                    $item->category?->name,
                    $item->unit,
                    $item->quantity_display,
                    $item->available_display,
                    $item->reorder_level,
                    $item->reorder_quantity,
                    $item->unit_cost ? number_format($item->unit_cost, 2) . ' ' . $item->currency : '',
                    $item->unit_price ? number_format($item->unit_price, 2) . ' ' . $item->currency : '',
                    number_format($item->stock_value, 2) . ' ' . $item->currency,
                    $item->is_organic_approved ? 'Yes' : 'No',
                    $item->stock_status,
                    $item->warehouse_location,
                    $item->bin_location,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}