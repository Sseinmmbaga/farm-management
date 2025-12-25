<?php

namespace App\Http\Controllers\Farms;

use App\Http\Controllers\Controller;
use App\Models\Farmers\Farmer;
use App\Models\Farms\FarmRecord;
use App\Models\Farms\Season;
use Illuminate\Http\Request;

class FarmerHistoryController extends Controller
{
    /**
     * Display farmer history for new farm records (Form 2)
     */
    public function newFarmHistory(Request $request)
    {
        $farmers = Farmer::with(['farms.farmRecords' => function ($query) {
            $query->where('record_type', 'new')->orderBy('created_at', 'desc');
        }])->orderBy('first_name')->get();

        $seasons = Season::orderBy('start_date', 'desc')->get();

        return view('farmer-history.new', compact('farmers', 'seasons'));
    }

    /**
     * Display farmer history for existing farm records (Form 3)
     */
    public function existingFarmHistory(Request $request)
    {
        $farmers = Farmer::with(['farms.farmRecords' => function ($query) {
            $query->where('record_type', 'existing')->orderBy('created_at', 'desc');
        }])->orderBy('first_name')->get();

        $seasons = Season::orderBy('start_date', 'desc')->get();

        return view('farmer-history.existing', compact('farmers', 'seasons'));
    }

    /**
     * Show history for a specific farmer
     */
    public function show(Farmer $farmer, Request $request)
    {
        $recordType = $request->get('type', 'all');

        $query = FarmRecord::whereHas('farm', function ($q) use ($farmer) {
            $q->where('farmer_id', $farmer->id);
        })->with(['farm', 'season']);

        if ($recordType !== 'all') {
            $query->where('record_type', $recordType);
        }

        $records = $query->orderBy('created_at', 'desc')->get();
        $seasons = Season::orderBy('start_date', 'desc')->get();

        return view('farmer-history.show', compact('farmer', 'records', 'seasons', 'recordType'));
    }
}
