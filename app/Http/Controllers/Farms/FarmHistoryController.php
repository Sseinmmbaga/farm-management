<?php

namespace App\Http\Controllers\Farms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FarmHistoryController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response('Farm history create form - to be implemented', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response('Farm history store - to be implemented', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response('Farm history show - to be implemented', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response('Farm history edit form - to be implemented', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response('Farm history update - to be implemented', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response('Farm history delete - to be implemented', 200);
    }
}
