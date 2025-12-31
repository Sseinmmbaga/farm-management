<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreStockCategoryRequest;
use App\Http\Requests\Stock\UpdateStockCategoryRequest;
use App\Models\Stock\StockCategory;
use Illuminate\Http\Request;

class StockCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = StockCategory::with('parent')
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('name_sw', 'like', "%{$search}%"))
            ->when($request->parent_id, fn($q, $id) => $q->where('parent_id', $id))
            ->when($request->active, fn($q, $active) => $q->where('is_active', $active === 'true'))
            ->ordered()
            ->paginate(20);

        $parentCategories = StockCategory::roots()->active()->ordered()->get();

        return view('stock.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $parentCategories = StockCategory::roots()->active()->ordered()->get();
        return view('stock.categories.create', compact('parentCategories'));
    }

    public function store(StoreStockCategoryRequest $request)
    {
        $category = StockCategory::create($request->validated());

        return redirect()
            ->route('stock-categories.show', $category)
            ->with('success', 'Stock category created successfully.');
    }

    public function show($id)
    {
        $category = StockCategory::with(['parent', 'children', 'items'])->findOrFail($id);
        return view('stock.categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = StockCategory::findOrFail($id);
        $parentCategories = StockCategory::roots()->active()->where('id', '!=', $id)->ordered()->get();
        return view('stock.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateStockCategoryRequest $request, $id)
    {
        $category = StockCategory::findOrFail($id);
        $category->update($request->validated());

        return redirect()
            ->route('stock-categories.show', $category)
            ->with('success', 'Stock category updated successfully.');
    }

    public function destroy($id)
    {
        $category = StockCategory::findOrFail($id);
        // Check if category has items or children
        if ($category->items()->exists()) {
            return back()->with('error', 'Cannot delete category that has stock items. Please reassign items first.');
        }
        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete category that has sub‑categories. Please delete or move sub‑categories first.');
        }
        $category->delete();

        return redirect()
            ->route('stock-categories.index')
            ->with('success', 'Stock category deleted successfully.');
    }
}