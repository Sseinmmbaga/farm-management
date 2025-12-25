<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrainingMaterialController extends Controller
{
    public function create()
    {
        return view('training.materials.create');
    }

    public function store(Request $request)
    {
        return response('Training material store - to be implemented', 200);
    }

    public function show($id)
    {
        return view('training.materials.show', compact('id'));
    }

    public function edit($id)
    {
        return view('training.materials.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Training material update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Training material delete - to be implemented', 200);
    }

    public function programMaterials($program)
    {
        return view('training.materials.program', compact('program'));
    }
}