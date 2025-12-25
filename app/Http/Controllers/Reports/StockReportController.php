<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index()
    {
        return view('reports.stock.index');
    }

    public function inventory()
    {
        return view('reports.stock.inventory');
    }

    public function movements()
    {
        return view('reports.stock.movements');
    }

    public function distributions()
    {
        return view('reports.stock.distributions');
    }

    public function valuation()
    {
        return view('reports.stock.valuation');
    }

    public function export()
    {
        return response('Stock report export - to be implemented', 200);
    }
}