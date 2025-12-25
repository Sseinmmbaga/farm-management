<?php

namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandAssetController extends Controller
{
    public function create()
    {
        return view('assets.land.create');
    }

    public function store(Request $request)
    {
        return response('Land asset store - to be implemented', 200);
    }

    public function show($id)
    {
        return view('assets.land.show', compact('id'));
    }

    public function edit($id)
    {
        return view('assets.land.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Land asset update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Land asset delete - to be implemented', 200);
    }
}