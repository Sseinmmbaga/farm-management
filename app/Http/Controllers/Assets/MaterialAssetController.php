<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaterialAssetController extends Controller
{
    public function create()
    {
        return view('assets.materials.create');
    }

    public function store(Request $request)
    {
        return response('Material asset store - to be implemented', 200);
    }

    public function show($id)
    {
        return view('assets.materials.show', compact('id'));
    }

    public function edit($id)
    {
        return view('assets.materials.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Material asset update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Material asset delete - to be implemented', 200);
    }
}