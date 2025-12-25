<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockRequestController extends Controller
{
    public function index()
    {
        return view('stock.requests.index');
    }

    public function create()
    {
        return view('stock.requests.create');
    }

    public function store(Request $request)
    {
        return response('Stock request store - to be implemented', 200);
    }

    public function show($request)
    {
        return view('stock.requests.show', compact('request'));
    }

    public function edit($id)
    {
        return view('stock.requests.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Stock request update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Stock request delete - to be implemented', 200);
    }

    public function approve($requestId)
    {
        return response('Stock request approve - to be implemented', 200);
    }

    public function reject($requestId)
    {
        return response('Stock request reject - to be implemented', 200);
    }

    public function fulfill($requestId)
    {
        return response('Stock request fulfill - to be implemented', 200);
    }
}