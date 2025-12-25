<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HarvestLogController extends Controller
{
    public function create()
    {
        return response('Harvest log create form - to be implemented', 200);
    }

    public function store(Request $request)
    {
        return response('Harvest log store - to be implemented', 200);
    }

    public function show($id)
    {
        return response('Harvest log show - to be implemented', 200);
    }

    public function edit($id)
    {
        return response('Harvest log edit form - to be implemented', 200);
    }

    public function update(Request $request, $id)
    {
        return response('Harvest log update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Harvest log delete - to be implemented', 200);
    }
}