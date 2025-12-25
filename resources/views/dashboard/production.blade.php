@extends('layouts.app')

@section('title', 'Production Manager Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Production Manager Dashboard</h1>
        <p class="lead">Welcome, Production Manager! Manage production plans and records here.</p>
        
        <div class="alert alert-info">
            <strong>Note:</strong> This is an empty dashboard shell. Production modules will be implemented here.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Production Plans</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Create and manage production schedules</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Production Records</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Track actual production output</p>
            </div>
        </div>
    </div>
</div>
@endsection