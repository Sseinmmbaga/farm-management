<?php

namespace App\Http\Controllers\Farmers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FarmerDocumentController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response('Farmer document create form - to be implemented', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response('Farmer document store - to be implemented', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response('Farmer document edit form - to be implemented', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response('Farmer document update - to be implemented', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response('Farmer document delete - to be implemented', 200);
    }
}
