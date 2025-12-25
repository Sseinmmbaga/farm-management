<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class YieldReportController extends Controller
{
    public function index()
    {
        return view('reports.yields.index');
    }

    public function bySeason()
    {
        return view('reports.yields.by-season');
    }

    public function byCrop()
    {
        return view('reports.yields.by-crop');
    }

    public function byRegion()
    {
        return view('reports.yields.by-region');
    }

    public function comparison()
    {
        return view('reports.yields.comparison');
    }

    public function export()
    {
        return response('Yield report export - to be implemented', 200);
    }
}