<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    public function index()
    {
        return view('stock.transactions.index');
    }

    public function intake()
    {
        return view('stock.transactions.intake');
    }

    public function storeIntake(Request $request)
    {
        return response('Stock intake store - to be implemented', 200);
    }

    public function issuance()
    {
        return view('stock.transactions.issuance');
    }

    public function storeIssuance(Request $request)
    {
        return response('Stock issuance store - to be implemented', 200);
    }
}