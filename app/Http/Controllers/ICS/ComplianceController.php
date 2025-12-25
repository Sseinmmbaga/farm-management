<?php

namespace App\Http\Controllers\ICS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response('Compliance index - to be implemented', 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response('Compliance create form - to be implemented', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response('Compliance store - to be implemented', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response('Compliance show - to be implemented', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response('Compliance edit form - to be implemented', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response('Compliance update - to be implemented', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response('Compliance delete - to be implemented', 200);
    }
}
