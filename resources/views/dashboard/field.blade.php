@extends('layouts.app')

@section('title', 'Field Extension Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Field Extension Dashboard</h1>
        <p class="lead">Welcome, Field Extension Officer! Provide agricultural support and extension services here.</p>
        
        <div class="alert alert-info">
            <strong>Note:</strong> This is an empty dashboard shell. Field extension modules will be implemented here.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Farm Visits</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Schedule and record farm visits</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Farmer Training</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Organize training sessions</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">Technical Support</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Provide agricultural advice</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Extension Activities</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex">
                    <button class="btn btn-primary" disabled>Plan Farm Visit</button>
                    <button class="btn btn-success" disabled>Record Observations</button>
                    <button class="btn btn-info" disabled>Schedule Training</button>
                    <button class="btn btn-warning" disabled>Submit Report</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Resources</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Agricultural guides, best practices, and technical resources will be available here.</p>
                <ul class="list-group">
                    <li class="list-group-item">Crop management guides: Coming soon</li>
                    <li class="list-group-item">Pest control information: Coming soon</li>
                    <li class="list-group-item">Soil testing procedures: Coming soon</li>
                    <li class="list-group-item">Weather advisories: Coming soon</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection