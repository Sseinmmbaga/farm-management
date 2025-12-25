<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComplianceReportController extends Controller
{
    public function index()
    {
        return view('reports.compliance.index');
    }

    public function inspections()
    {
        return view('reports.compliance.inspections');
    }

    public function findings()
    {
        return view('reports.compliance.findings');
    }

    public function certifications()
    {
        return view('reports.compliance.certifications');
    }

    public function export()
    {
        return response('Compliance report export - to be implemented', 200);
    }
}