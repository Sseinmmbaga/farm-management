@extends('layouts.app')

@section('title', 'Accountant Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Accountant Dashboard</h1>
        <p class="lead">Welcome, Accountant! Manage financial transactions and reports here.</p>
        
        <div class="alert alert-info">
            <strong>Note:</strong> This is an empty dashboard shell. Financial modules will be implemented here.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Financial Overview</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Revenue, expenses, and profit reports</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Transactions</h5>
            </div>
            <div class="card-body text-center">
                <h2>Coming Soon</h2>
                <p class="text-muted">Manage payments and invoices</p>
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
                <p class="text-muted">Generate financial statements</p>
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
                    <button class="btn btn-primary" disabled>Record Expense</button>
                    <button class="btn btn-success" disabled>Record Sale</button>
                    <button class="btn btn-info" disabled>Generate Report</button>
                    <button class="btn btn-warning" disabled>View Ledger</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection