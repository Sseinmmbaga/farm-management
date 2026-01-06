<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Ward;
use App\Models\Location\Village;
use App\Models\Location\Subvillage;

class CatchmentAreaController extends Controller
{
    public function index()
    {
        $stats = [
            'regions' => Region::count(),
            'districts' => District::count(),
            'wards' => Ward::count(),
            'villages' => Village::count(),
            'subvillages' => Subvillage::count(),
        ];

        $regions = Region::withCount(['districts', 'villages', 'subvillages'])
            ->orderBy('name')
            ->get();

        return view('catchment-areas.index', compact('stats', 'regions'));
    }
}
