<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index($session)
    {
        return view('training.attendance.index', compact('session'));
    }

    public function store(Request $request, $session)
    {
        return response('Attendance store - to be implemented', 200);
    }

    public function update(Request $request, $session, $attendance)
    {
        return response('Attendance update - to be implemented', 200);
    }

    public function bulkStore(Request $request, $session)
    {
        return response('Bulk attendance store - to be implemented', 200);
    }

    public function checkIn($session, $farmer)
    {
        return response('Check-in - to be implemented', 200);
    }

    public function checkOut($session, $farmer)
    {
        return response('Check-out - to be implemented', 200);
    }
}