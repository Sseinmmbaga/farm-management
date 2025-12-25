<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CropAssetController extends Controller
{
    public function create()
    {
        return view('assets.crops.create');
    }

    public function store(Request $request)
    {
        return response('Crop asset store - to be implemented', 200);
    }

    public function show($id)
    {
        return view('assets.crops.show', compact('id'));
    }

    public function edit($id)
    {
        return view('assets.crops.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Crop asset update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Crop asset delete - to be implemented', 200);
    }

    public function updateStage($cropAsset)
    {
        return response('Crop asset update stage - to be implemented', 200);
    }
}