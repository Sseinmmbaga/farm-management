<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InputLogController extends Controller
{
    public function create()
    {
        return response('Input log create form - to be implemented', 200);
    }

    public function store(Request $request)
    {
        return response('Input log store - to be implemented', 200);
    }

    public function show($id)
    {
        return response('Input log show - to be implemented', 200);
    }

    public function edit($id)
    {
        return response('Input log edit form - to be implemented', 200);
    }

    public function update(Request $request, $id)
    {
        return response('Input log update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Input log delete - to be implemented', 200);
    }
}