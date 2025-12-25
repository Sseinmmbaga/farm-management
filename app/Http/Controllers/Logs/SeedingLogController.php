<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeedingLogController extends Controller
{
    public function create()
    {
        return response('Seeding log create form - to be implemented', 200);
    }

    public function store(Request $request)
    {
        return response('Seeding log store - to be implemented', 200);
    }

    public function show($id)
    {
        return response('Seeding log show - to be implemented', 200);
    }

    public function edit($id)
    {
        return response('Seeding log edit form - to be implemented', 200);
    }

    public function update(Request $request, $id)
    {
        return response('Seeding log update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Seeding log delete - to be implemented', 200);
    }
}