@extends('layouts.app')

@section('title', 'ICS Inspector Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">ICS Inspector Dashboard</h1>
        <p class="lead">Welcome, ICS Inspector! Manage farm inspections and certifications here.</p>
        
        <div class="alert alert-info">
            <strong>Note:</strong> This is an empty dashboard shell. ICS inspection modules will be implemented here.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Pending Inspections</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Farms awaiting inspection</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Completed Inspections</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Recently inspected farms</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">Certifications</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Manage certification status</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex">
                    <button class="btn btn-primary" disabled>Schedule Inspection</button>
                    <button class="btn btn-success" disabled>Record Findings</button>
                    <button class="btn btn-info" disabled>Update Certification</button>
                    <button class="btn btn-warning" disabled>Generate Report</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection