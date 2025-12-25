@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Supervisor Dashboard</h1>
        <p class="lead">Welcome, Supervisor! Monitor field operations and team activities here.</p>
        
        <div class="alert alert-info">
            <strong>Note:</strong> This is an empty dashboard shell. Supervision modules will be implemented here.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Team Members</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Manage your supervision team</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Field Activities</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Monitor ongoing field work</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">Reports</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Generate supervision reports</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Supervision Tasks</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">Team assignments: Coming soon</li>
                    <li class="list-group-item">Field visit schedules: Coming soon</li>
                    <li class="list-group-item">Progress monitoring: Coming soon</li>
                    <li class="list-group-item">Quality checks: Coming soon</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection