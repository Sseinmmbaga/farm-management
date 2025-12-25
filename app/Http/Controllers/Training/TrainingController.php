<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        return view('training.programs.index');
    }

    public function create()
    {
        return view('training.programs.create');
    }

    public function store(Request $request)
    {
        return response('Training program store - to be implemented', 200);
    }

    public function show($id)
    {
        return view('training.programs.show', compact('id'));
    }

    public function edit($id)
    {
        return view('training.programs.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return response('Training program update - to be implemented', 200);
    }

    public function destroy($id)
    {
        return response('Training program delete - to be implemented', 200);
    }

    // For training sessions (same resource but different name)
    public function upcoming()
    {
        return view('training.sessions.upcoming');
    }

    public function completed()
    {
        return view('training.sessions.completed');
    }

    public function cancel($session)
    {
        return response('Training session cancel - to be implemented', 200);
    }

    public function complete($session)
    {
        return response('Training session complete - to be implemented', 200);
    }

    public function issueCertificates($session)
    {
        return response('Issue certificates - to be implemented', 200);
    }

    public function reportSummary()
    {
        return view('training.reports.summary');
    }

    public function reportAttendance()
    {
        return view('training.reports.attendance');
    }

    public function farmerTrainingHistory($farmer)
    {
        return view('training.reports.farmer', compact('farmer'));
    }
}