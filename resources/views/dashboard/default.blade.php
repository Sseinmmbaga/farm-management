@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Dashboard</h1>
        <p class="lead">Welcome to the Remei Farm OS!</p>
        
        <div class="alert alert-warning">
            <strong>Notice:</strong> Your user role does not have a specific dashboard configured. Please contact the system administrator.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Available Actions</h5>
            </div>
            <div class="card-body">
                <p>Based on your permissions, you may have access to the following:</p>
                <ul>
                    <li><a href="{{ route('dashboard') }}">Main Dashboard</a></li>
                    <li><a href="{{ route('users.index') }}">User Management</a> (if authorized)</li>
                    <li><a href="{{ route('farmers.index') }}">Farmer Management</a> (if authorized)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <p>Remei Farm OS - Farm Management System</p>
                <ul class="list-group">
                    <li class="list-group-item">Version: 1.0.0</li>
                    <li class="list-group-item">Role-Based Access Control: Active</li>
                    <li class="list-group-item">Multi-User System: Active</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Need Help?</h5>
            </div>
            <div class="card-body">
                <p>If you believe you should have access to specific dashboard features, please contact your system administrator.</p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary">Contact Support</a>
                    <a href="{{ route('logout') }}" class="btn btn-outline-secondary">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection