<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ObservationLogController extends Controller
{
    public function create()
    {
        return response('Observation log create form - to be implemented', 200);
    }

    public function store(Request $request)
    {
        return response('Observation log store - to be implemented', 200);
    }

    public function show($id)
    {
        return response('Observation log show - to be implemented', 200);
    }

    public function edit($id)
    {
        return response('Observation log edit form - to be implemented', 200);
    }

    public function update(Request $request, $id)
    {
        return response('Observation log update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Observation log delete - to be implemented', 200);
    }
}