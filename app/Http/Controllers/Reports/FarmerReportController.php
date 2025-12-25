<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Farmers\Farmer;
use App\Models\Location\Region;
use Illuminate\Http\Request;

class FarmerReportController extends Controller
{
    public function index()
    {
        $totalFarmers = Farmer::count();
        $activeFarmers = Farmer::active()->count();
        $pendingFarmers = Farmer::pending()->count();
        
        return view('reports.farmers.index', compact(
            'totalFarmers',
            'activeFarmers',
            'pendingFarmers'
        ));
    }

    public function byRegion()
    {
        $regions = Region::withCount(['farmers'])->get();
        
        return view('reports.farmers.by-region', compact('regions'));
    }

    public function byStatus()
    {
        $statusCounts = Farmer::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        return view('reports.farmers.by-status', compact('statusCounts'));
    }

    public function byCertification()
    {
        $certificationCounts = Farmer::selectRaw('certification_status, COUNT(*) as count')
            ->groupBy('certification_status')
            ->get();
        
        return view('reports.farmers.by-certification', compact('certificationCounts'));
    }

    public function registrations()
    {
        $monthlyRegistrations = Farmer::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();
        
        return view('reports.farmers.registrations', compact('monthlyRegistrations'));
    }

    public function export()
    {
        return response('Farmer report export - to be implemented', 200);
    }
}