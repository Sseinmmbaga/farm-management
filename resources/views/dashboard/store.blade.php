@extends('layouts.app')

@section('title', 'Storekeeper Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Storekeeper Dashboard</h1>
        <p class="lead">Welcome, Storekeeper! Manage inventory and store operations here.</p>
        
        <div class="alert alert-info">
            <strong>Note:</strong> This is an empty dashboard shell. Inventory management modules will be implemented here.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Inventory</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Track stock levels</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Issuances</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Manage item distributions</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">Requisitions</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Process stock requests</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Store Operations</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex">
                    <button class="btn btn-primary" disabled>Receive Stock</button>
                    <button class="btn btn-success" disabled>Issue Items</button>
                    <button class="btn btn-info" disabled>Take Inventory</button>
                    <button class="btn btn-warning" disabled>Generate Report</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Low Stock Items</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Items needing replenishment will appear here.</p>
                <ul class="list-group">
                    <li class="list-group-item">Fertilizers: Coming soon</li>
                    <li class="list-group-item">Seeds: Coming soon</li>
                    <li class="list-group-item">Tools: Coming soon</li>
                    <li class="list-group-item">Protective gear: Coming soon</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Recent store transactions will be listed here.</p>
                <ul class="list-group">
                    <li class="list-group-item">Stock receipts: Coming soon</li>
                    <li class="list-group-item">Item issuances: Coming soon</li>
                    <li class="list-group-item">Inventory adjustments: Coming soon</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection