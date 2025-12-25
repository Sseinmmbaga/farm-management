<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquipmentAssetController extends Controller
{
    public function create()
    {
        return view('assets.equipment.create');
    }

    public function store(Request $request)
    {
        return response('Equipment asset store - to be implemented', 200);
    }

    public function show($id)
    {
        return view('assets.equipment.show', compact('id'));
    }

    public function edit($id)
    {
        return view('assets.equipment.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Equipment asset update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Equipment asset delete - to be implemented', 200);
    }

    public function recordMaintenance($equipmentAsset)
    {
        return response('Equipment asset maintenance - to be implemented', 200);
    }
}