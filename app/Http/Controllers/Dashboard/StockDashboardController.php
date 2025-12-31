<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Stock\StockItem;
use App\Models\Stock\StockCategory;
use App\Models\Stock\StockRequest;
use App\Models\Stock\StockTransaction;
use App\Models\Stock\StockDistribution;
use Illuminate\Http\Request;

class StockDashboardController extends Controller
{
    public function index()
    {
        // Real stats for dashboard
        $totalItems = StockItem::active()->count();
        $lowStockItems = StockItem::active()->lowStock()->count();
        $pendingDeliveries = StockTransaction::pending()->intakes()->count();
        $pendingRequests = StockRequest::whereIn('status', ['submitted', 'approved'])->count();

        // Additional stats for dashboard
        $totalStockValue = StockItem::active()->get()->sum('stock_value');
        $criticalStockItems = StockItem::active()->criticalStock()->count();
        $outOfStockItems = StockItem::active()->outOfStock()->count();

        // Recent activity
        $recentTransactions = StockTransaction::with(['stockItem', 'farmer'])
            ->latest()
            ->take(5)
            ->get();

        $recentRequests = StockRequest::with(['requestedBy', 'farmer'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly stats
        $monthlyIntakes = StockTransaction::intakes()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('quantity');

        $monthlyIssuances = StockTransaction::issuances()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('quantity');

        $monthlyDistributions = StockDistribution::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Low stock items list for quick view
        $lowStockItemsList = StockItem::with('category')
            ->active()
            ->lowStock()
            ->orderByRaw('quantity_available / NULLIF(reorder_level, 0) ASC')
            ->take(10)
            ->get();

        // Stock by category for chart
        $stockByCategory = StockCategory::withCount(['items as total_items' => function ($q) {
                $q->active();
            }])
            ->withSum(['items as total_value' => function ($q) {
                $q->active();
            }], 'quantity_on_hand')
            ->active()
            ->get();

        return view('dashboard.stock-manager.index', compact(
            'totalItems',
            'lowStockItems',
            'pendingDeliveries',
            'pendingRequests',
            'totalStockValue',
            'criticalStockItems',
            'outOfStockItems',
            'recentTransactions',
            'recentRequests',
            'monthlyIntakes',
            'monthlyIssuances',
            'monthlyDistributions',
            'lowStockItemsList',
            'stockByCategory'
        ));
    }

    public function inventory(Request $request)
    {
        // Real inventory stats
        $stats = [
            'total_items' => StockItem::count(),
            'in_stock' => StockItem::active()->inStock()->count(),
            'low_stock' => StockItem::active()->lowStock()->count(),
            'out_of_stock' => StockItem::active()->outOfStock()->count(),
        ];

        $items = StockItem::with('category')
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->category_id, fn($q, $id) => $q->inCategory($id))
            ->when($request->status, function ($q, $status) {
                match ($status) {
                    'low' => $q->lowStock(),
                    'critical' => $q->criticalStock(),
                    'out' => $q->outOfStock(),
                    'active' => $q->active(),
                    default => $q,
                };
            })
            ->active()
            ->latest()
            ->paginate(20);

        $categories = StockCategory::active()->ordered()->get();

        return view('dashboard.stock-manager.inventory', compact('stats', 'items', 'categories'));
    }

    public function alerts(Request $request)
    {
        // Real alerts stats
        $criticalAlerts = StockItem::active()->criticalStock()->count();
        $warningAlerts = StockItem::active()->lowStock()->whereRaw('quantity_available > (reorder_level * 0.5)')->count();
        $outOfStockAlerts = StockItem::active()->outOfStock()->count();

        $stats = [
            'critical_alerts' => $criticalAlerts,
            'warning_alerts' => $warningAlerts,
            'out_of_stock_alerts' => $outOfStockAlerts,
            'total_alerts' => $criticalAlerts + $warningAlerts + $outOfStockAlerts,
        ];

        // Get all alert items
        $alerts = StockItem::with('category')
            ->active()
            ->where(function ($q) {
                $q->lowStock()->orWhere('quantity_available', '<=', 0);
            })
            ->orderByRaw('CASE
                WHEN quantity_available <= 0 THEN 1
                WHEN quantity_available <= (reorder_level * 0.5) THEN 2
                ELSE 3
            END')
            ->paginate(20);

        return view('dashboard.stock-manager.alerts', compact('stats', 'alerts'));
    }

    public function distributions(Request $request)
    {
        $stats = [
            'total_distributions' => StockDistribution::count(),
            'this_month' => StockDistribution::whereMonth('created_at', now()->month)->count(),
            'credit_outstanding' => StockDistribution::credit()->where('is_repaid', false)->sum('value'),
            'farmers_served' => StockDistribution::distinct('farmer_id')->count('farmer_id'),
        ];

        $distributions = StockDistribution::with(['farmer', 'stockItem', 'distributedBy'])
            ->when($request->search, function ($q, $search) {
                $q->whereHas('farmer', function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($request->type, fn($q, $type) => $q->where('distribution_type', $type))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(20);

        return view('dashboard.stock-manager.distributions', compact('stats', 'distributions'));
    }

    public function requests(Request $request)
    {
        $stats = [
            'total_requests' => StockRequest::count(),
            'pending_approval' => StockRequest::submitted()->count(),
            'pending_fulfillment' => StockRequest::approved()->count(),
            'fulfilled_this_month' => StockRequest::fulfilled()
                ->whereMonth('fulfilled_at', now()->month)
                ->count(),
        ];

        $requests = StockRequest::with(['requestedBy', 'farmer', 'items.stockItem'])
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->priority, fn($q, $priority) => $q->where('priority', $priority))
            ->latest()
            ->paginate(20);

        return view('dashboard.stock-manager.requests', compact('stats', 'requests'));
    }
}
