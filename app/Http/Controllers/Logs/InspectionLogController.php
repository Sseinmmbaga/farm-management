<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InspectionLogController extends Controller
{
    public function create()
    {
        return response('Inspection log create form - to be implemented', 200);
    }

    public function store(Request $request)
    {
        return response('Inspection log store - to be implemented', 200);
    }

    public function show($id)
    {
        return response('Inspection log show - to be implemented', 200);
    }

    public function edit($id)
    {
        return response('Inspection log edit form - to be implemented', 200);
    }

    public function update(Request $request, $id)
    {
        return response('Inspection log update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Inspection log delete - to be implemented', 200);
    }
}