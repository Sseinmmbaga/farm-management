<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreStockItemRequest;
use App\Http\Requests\Stock\UpdateStockItemRequest;
use App\Models\Stock\StockItem;
use App\Models\Stock\StockCategory;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $stockItems = StockItem::with('category')
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->category_id, fn($q, $id) => $q->inCategory($id))
            ->when($request->status, function ($q, $status) {
                if ($status === 'low') $q->lowStock();
                elseif ($status === 'critical') $q->criticalStock();
                elseif ($status === 'out') $q->outOfStock();
                elseif ($status === 'active') $q->active();
            })
            ->latest()
            ->paginate(20);

        $categories = StockCategory::active()->ordered()->get();

        // Calculate stats from full database, not paginated results
        $stats = [
            'total_items' => StockItem::count(),
            'in_stock' => StockItem::where('quantity_available', '>', 0)->count(),
            'low_stock' => StockItem::lowStock()->count(),
            'out_of_stock' => StockItem::outOfStock()->count(),
        ];

        return view('stock.index', compact('stockItems', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = StockCategory::active()->ordered()->get();
        $units = config('units.units');
        return view('stock.create', compact('categories', 'units'));
    }

    public function store(StoreStockItemRequest $request)
    {
        $stockItem = StockItem::create($request->validated());

        return redirect()
            ->route('stock.show', $stockItem)
            ->with('success', 'Stock item created successfully.');
    }

    public function show($id)
    {
        $stockItem = StockItem::with(['category', 'transactions', 'batches'])->findOrFail($id);
        return view('stock.show', compact('stockItem'));
    }

    public function edit($id)
    {
        $stockItem = StockItem::findOrFail($id);
        $categories = StockCategory::active()->ordered()->get();
        $units = config('units.units');
        return view('stock.edit', compact('stockItem', 'categories', 'units'));
    }

    public function update(UpdateStockItemRequest $request, $id)
    {
        $stockItem = StockItem::findOrFail($id);
        $stockItem->update($request->validated());

        return redirect()
            ->route('stock.show', $stockItem)
            ->with('success', 'Stock item updated successfully.');
    }

    public function destroy($id)
    {
        $stockItem = StockItem::findOrFail($id);
        $stockItem->delete();

        return redirect()
            ->route('stock.index')
            ->with('success', 'Stock item deleted successfully.');
    }

    public function lowStock()
    {
        $stockItems = StockItem::with('category')
            ->lowStock()
            ->active()
            ->latest()
            ->paginate(20);

        return view('stock.alerts.low-stock', compact('stockItems'));
    }

    public function criticalStock()
    {
        $stockItems = StockItem::with('category')
            ->criticalStock()
            ->active()
            ->latest()
            ->paginate(20);

        return view('stock.alerts.critical', compact('stockItems'));
    }

    public function outOfStock()
    {
        $stockItems = StockItem::with('category')
            ->outOfStock()
            ->active()
            ->latest()
            ->paginate(20);

        return view('stock.alerts.out-of-stock', compact('stockItems'));
    }

    public function reportSummary()
    {
        $stockItems = StockItem::with('category')->active()->get();
        $categories = StockCategory::with('items')->active()->get();

        $stats = [
            'total_items' => $stockItems->count(),
            'total_value' => $stockItems->sum('stock_value'),
            'low_stock' => $stockItems->filter(fn($i) => $i->is_low_stock)->count(),
            'out_of_stock' => $stockItems->filter(fn($i) => $i->is_out_of_stock)->count(),
            'categories_count' => $categories->count(),
        ];

        return view('stock.reports.summary', compact('stockItems', 'categories', 'stats'));
    }

    public function reportMovements(Request $request)
    {
        $transactions = \App\Models\Stock\StockTransaction::with(['stockItem', 'farmer'])
            ->when($request->date_from, fn($q, $date) => $q->whereDate('transaction_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('transaction_date', '<=', $date))
            ->when($request->type, fn($q, $type) => $q->where('transaction_type', $type))
            ->when($request->stock_item_id, fn($q, $id) => $q->where('stock_item_id', $id))
            ->latest()
            ->paginate(50);

        $stockItems = StockItem::active()->get(['id', 'name', 'code']);

        return view('stock.reports.movements', compact('transactions', 'stockItems'));
    }

    public function reportValuation()
    {
        $stockItems = StockItem::with('category')
            ->active()
            ->orderBy('category_id')
            ->get();

        $categories = StockCategory::with(['items' => fn($q) => $q->active()])
            ->active()
            ->get();

        $totalValuation = $stockItems->sum('stock_value');

        return view('stock.reports.valuation', compact('stockItems', 'categories', 'totalValuation'));
    }

    public function exportCsv()
    {
        $stockItems = StockItem::with('category')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_inventory_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($stockItems) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'Code',
                'SKU',
                'Name',
                'Name (Swahili)',
                'Category',
                'Unit',
                'Quantity On Hand',
                'Quantity Reserved',
                'Quantity Available',
                'Reorder Level',
                'Unit Cost',
                'Stock Value',
                'Status',
                'Is Active',
            ]);

            // CSV Data
            foreach ($stockItems as $item) {
                fputcsv($file, [
                    $item->code,
                    $item->sku,
                    $item->name,
                    $item->name_sw,
                    $item->category?->name ?? 'Uncategorized',
                    $item->unit,
                    $item->quantity_on_hand,
                    $item->quantity_reserved,
                    $item->quantity_available,
                    $item->reorder_level,
                    $item->unit_cost,
                    $item->stock_value,
                    $item->stock_status,
                    $item->is_active ? 'Yes' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSummaryCsv()
    {
        $stockItems = StockItem::with('category')->active()->get();
        $categories = StockCategory::with('items')->active()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_summary_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($stockItems, $categories) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, ['STOCK SUMMARY REPORT']);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);

            // Summary Stats
            fputcsv($file, ['SUMMARY STATISTICS']);
            fputcsv($file, ['Total Items', $stockItems->count()]);
            fputcsv($file, ['Total Value (TZS)', number_format($stockItems->sum('stock_value'), 2)]);
            fputcsv($file, ['Low Stock Items', $stockItems->filter(fn($i) => $i->is_low_stock)->count()]);
            fputcsv($file, ['Out of Stock Items', $stockItems->filter(fn($i) => $i->is_out_of_stock)->count()]);
            fputcsv($file, []);

            // Stock by Category
            fputcsv($file, ['STOCK BY CATEGORY']);
            fputcsv($file, ['Category', 'Code', 'Items Count', 'Total Value (TZS)']);
            foreach ($categories as $category) {
                $categoryItems = $stockItems->where('category_id', $category->id);
                fputcsv($file, [
                    $category->name,
                    $category->code,
                    $categoryItems->count(),
                    number_format($categoryItems->sum('stock_value'), 2),
                ]);
            }
            fputcsv($file, []);

            // Detailed Items
            fputcsv($file, ['DETAILED STOCK LIST']);
            fputcsv($file, ['Code', 'Name', 'Category', 'Quantity', 'Unit', 'Unit Cost', 'Stock Value', 'Status']);
            foreach ($stockItems->sortByDesc('stock_value') as $item) {
                fputcsv($file, [
                    $item->code,
                    $item->name,
                    $item->category?->name ?? 'Uncategorized',
                    $item->quantity_on_hand,
                    $item->unit,
                    $item->unit_cost,
                    number_format($item->stock_value, 2),
                    $item->stock_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportMovementsCsv(Request $request)
    {
        $transactions = \App\Models\Stock\StockTransaction::with(['stockItem', 'farmer'])
            ->when($request->date_from, fn($q, $date) => $q->whereDate('transaction_date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('transaction_date', '<=', $date))
            ->when($request->type, fn($q, $type) => $q->where('transaction_type', $type))
            ->when($request->stock_item_id, fn($q, $id) => $q->where('stock_item_id', $id))
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_movements_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($transactions, $request) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, ['STOCK MOVEMENTS REPORT']);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y H:i:s')]);
            if ($request->date_from || $request->date_to) {
                fputcsv($file, ['Date Range: ' . ($request->date_from ?? 'Start') . ' to ' . ($request->date_to ?? 'End')]);
            }
            fputcsv($file, []);

            // Summary Stats
            $intakes = $transactions->filter(fn($t) => ($t->transaction_type->value ?? $t->transaction_type) === 'intake');
            $issuances = $transactions->filter(fn($t) => ($t->transaction_type->value ?? $t->transaction_type) === 'issuance');
            $distributions = $transactions->filter(fn($t) => ($t->transaction_type->value ?? $t->transaction_type) === 'distribution');

            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Transactions', $transactions->count()]);
            fputcsv($file, ['Intakes', $intakes->count(), 'Quantity: +' . number_format($intakes->sum('quantity'), 2)]);
            fputcsv($file, ['Issuances', $issuances->count(), 'Quantity: -' . number_format($issuances->sum('quantity'), 2)]);
            fputcsv($file, ['Distributions', $distributions->count(), 'Quantity: -' . number_format($distributions->sum('quantity'), 2)]);
            fputcsv($file, ['Total Value (TZS)', number_format($transactions->sum('total_cost'), 2)]);
            fputcsv($file, []);

            // Transaction Details
            fputcsv($file, ['TRANSACTION DETAILS']);
            fputcsv($file, [
                'Date',
                'Reference',
                'Type',
                'Stock Item',
                'Item Code',
                'Quantity',
                'Balance Before',
                'Balance After',
                'Unit Cost',
                'Total Value',
                'Farmer',
                'Notes'
            ]);

            foreach ($transactions as $transaction) {
                $type = $transaction->transaction_type->value ?? $transaction->transaction_type;
                $isIncoming = in_array($type, ['intake', 'return']);

                fputcsv($file, [
                    $transaction->transaction_date?->format('Y-m-d H:i') ?? $transaction->created_at->format('Y-m-d H:i'),
                    $transaction->reference_number,
                    ucfirst($type),
                    $transaction->stockItem?->name ?? 'N/A',
                    $transaction->stockItem?->code ?? 'N/A',
                    ($isIncoming ? '+' : '-') . number_format($transaction->quantity, 2),
                    number_format($transaction->balance_before, 2),
                    number_format($transaction->balance_after, 2),
                    number_format($transaction->unit_cost, 2),
                    number_format($transaction->total_cost, 2),
                    $transaction->farmer ? $transaction->farmer->first_name . ' ' . $transaction->farmer->last_name : '-',
                    $transaction->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportValuationCsv()
    {
        $stockItems = StockItem::with('category')->active()->orderBy('category_id')->get();
        $categories = StockCategory::with(['items' => fn($q) => $q->active()])->active()->get();
        $totalValuation = $stockItems->sum('stock_value');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_valuation_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($stockItems, $categories, $totalValuation) {
            $file = fopen('php://output', 'w');

            // Report Header
            fputcsv($file, ['STOCK VALUATION REPORT']);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y H:i:s')]);
            fputcsv($file, ['Generated By: ' . (auth()->user()->name ?? 'System')]);
            fputcsv($file, []);

            // Total Valuation
            fputcsv($file, ['TOTAL STOCK VALUATION']);
            fputcsv($file, ['Total Value (TZS)', number_format($totalValuation, 2)]);
            fputcsv($file, ['Total Items', $stockItems->count()]);
            fputcsv($file, ['Total Categories', $categories->count()]);
            fputcsv($file, []);

            // Valuation by Category
            fputcsv($file, ['VALUATION BY CATEGORY']);
            fputcsv($file, ['Category', 'Code', 'Items', 'Total Quantity', 'Total Value (TZS)', '% of Total']);
            foreach ($categories as $category) {
                $categoryItems = $stockItems->where('category_id', $category->id);
                $categoryValue = $categoryItems->sum('stock_value');
                $percentage = $totalValuation > 0 ? ($categoryValue / $totalValuation) * 100 : 0;

                fputcsv($file, [
                    $category->name,
                    $category->code,
                    $categoryItems->count(),
                    number_format($categoryItems->sum('quantity_on_hand'), 2),
                    number_format($categoryValue, 2),
                    number_format($percentage, 1) . '%',
                ]);
            }
            fputcsv($file, []);

            // Detailed Valuation
            fputcsv($file, ['DETAILED STOCK VALUATION']);
            fputcsv($file, [
                'Code',
                'SKU',
                'Name',
                'Category',
                'Quantity On Hand',
                'Quantity Reserved',
                'Quantity Available',
                'Unit',
                'Unit Cost (TZS)',
                'Stock Value (TZS)',
                'Status'
            ]);

            foreach ($stockItems as $item) {
                fputcsv($file, [
                    $item->code,
                    $item->sku ?? '-',
                    $item->name,
                    $item->category?->name ?? 'Uncategorized',
                    number_format($item->quantity_on_hand, 2),
                    number_format($item->quantity_reserved, 2),
                    number_format($item->quantity_available, 2),
                    $item->unit,
                    number_format($item->unit_cost, 2),
                    number_format($item->stock_value, 2),
                    $item->stock_status,
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', '', '', '', 'TOTAL:', number_format($totalValuation, 2), '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}