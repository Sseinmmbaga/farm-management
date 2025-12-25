<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockCategoryController extends Controller
{
    public function index()
    {
        return view('stock.categories.index');
    }

    public function create()
    {
        return view('stock.categories.create');
    }

    public function store(Request $request)
    {
        return response('Stock category store - to be implemented', 200);
    }

    public function show($id)
    {
        return view('stock.categories.show', compact('id'));
    }

    public function edit($id)
    {
        return view('stock.categories.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Stock category update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Stock category delete - to be implemented', 200);
    }
}