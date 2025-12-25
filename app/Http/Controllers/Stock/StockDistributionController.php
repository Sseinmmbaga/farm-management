<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockDistributionController extends Controller
{
    public function index()
    {
        return view('stock.distributions.index');
    }

    public function create()
    {
        return view('stock.distributions.create');
    }

    public function store(Request $request)
    {
        return response('Stock distribution store - to be implemented', 200);
    }

    public function show($distribution)
    {
        return view('stock.distributions.show', compact('distribution'));
    }

    public function farmerDistributions($farmer)
    {
        return view('stock.distributions.farmer', compact('farmer'));
    }
}