<?php

namespace App\Http\Controllers\Farms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FarmSeasonController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response('Farm season create form - to be implemented', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response('Farm season store - to be implemented', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response('Farm season show - to be implemented', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response('Farm season edit form - to be implemented', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response('Farm season update - to be implemented', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response('Farm season delete - to be implemented', 200);
    }
}
