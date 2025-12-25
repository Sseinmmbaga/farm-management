<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        return view('stock.index');
    }

    public function create()
    {
        return view('stock.create');
    }

    public function store(Request $request)
    {
        return response('Stock store - to be implemented', 200);
    }

    public function show($id)
    {
        return view('stock.show', compact('id'));
    }

    public function edit($id)
    {
        return view('stock.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Stock update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Stock delete - to be implemented', 200);
    }

    public function lowStock()
    {
        return view('stock.alerts.low-stock');
    }

    public function criticalStock()
    {
        return view('stock.alerts.critical');
    }

    public function outOfStock()
    {
        return view('stock.alerts.out-of-stock');
    }

    public function reportSummary()
    {
        return view('stock.reports.summary');
    }

    public function reportMovements()
    {
        return view('stock.reports.movements');
    }

    public function reportValuation()
    {
        return view('stock.reports.valuation');
    }
}