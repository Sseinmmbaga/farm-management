@extends('layouts.base')

@section('title', 'Stock Distributions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-users me-2"></i> Stock Distributions to Farmers
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('stock.distributions.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle me-1"></i> New Distribution
                            </a>
                            <a href="{{ route('stock.index') }}" class="btn btn-light">
                                <i class="fas fa-boxes me-1"></i> Stock Inventory
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Distributions</h6>
                                            <h3 class="mb-0">{{ $distributions->total() }}</h3>
                                        </div>
                                        <div class="bg-primary text-white rounded-circle p-3">
                                            <i class="fas fa-exchange-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Credit</h6>
                                            <h3 class="mb-0 text-warning">{{ $distributions->where('distribution_type', 'credit')->count() }}</h3>
                                        </div>
                                        <div class="bg-warning text-white rounded-circle p-3">
                                            <i class="fas fa-credit-card fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Cash</h6>
                                            <h3 class="mb-0 text-success">{{ $distributions->where('distribution_type', 'cash')->count() }}</h3>
                                        </div>
                                        <div class="bg-success text-white rounded-circle p-3">
                                            <i class="fas fa-money-bill fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Free</h6>
                                            <h3 class="mb-0 text-info">{{ $distributions->where('distribution_type', 'free')->count() }}</h3>
                                        </div>
                                        <div class="bg-info text-white rounded-circle p-3">
                                            <i class="fas fa-gift fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('stock.distributions.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search farmer name..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                                <option value="cash" {{ request('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>Free</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="repaid" class="form-select" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="repaid" {{ request('repaid') == 'repaid' ? 'selected' : '' }}>Repaid</option>
                                <option value="pending" {{ request('repaid') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="overdue" {{ request('repaid') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        @if(request('search') || request('type') || request('repaid'))
                            <div class="col-md-1">
                                <a href="{{ route('stock.distributions.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Distributions Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Farmer</th>
                                    <th>Stock Item</th>
                                    <th>Quantity</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Season</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distributions as $distribution)
                                    <tr>
                                        <td>
                                            {{ $distribution->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $distribution->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            @if($distribution->farmer)
                                                <a href="{{ route('stock.distributions.farmer', $distribution->farmer) }}">
                                                    <strong>{{ $distribution->farmer->first_name }} {{ $distribution->farmer->last_name }}</strong>
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $distribution->farmer->registration_number ?? 'No Reg.' }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($distribution->transaction && $distribution->transaction->stockItem)
                                                <a href="{{ route('stock.show', $distribution->transaction->stockItem) }}">
                                                    {{ $distribution->transaction->stockItem->name }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $distribution->transaction->stockItem->code }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ number_format($distribution->quantity, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $distribution->transaction->stockItem->unit ?? 'units' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'credit' => 'warning',
                                                    'cash' => 'success',
                                                    'free' => 'info',
                                                ];
                                                $typeIcons = [
                                                    'credit' => 'credit-card',
                                                    'cash' => 'money-bill',
                                                    'free' => 'gift',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $typeColors[$distribution->distribution_type] ?? 'secondary' }}">
                                                <i class="fas fa-{{ $typeIcons[$distribution->distribution_type] ?? 'circle' }} me-1"></i>
                                                {{ ucfirst($distribution->distribution_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>TZS {{ number_format($distribution->value, 2) }}</strong>
                                            @if($distribution->distribution_type === 'credit' && !$distribution->is_repaid)
                                                <br>
                                                <small class="text-muted">
                                                    Repaid: TZS {{ number_format($distribution->amount_repaid, 2) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($distribution->distribution_type === 'credit')
                                                @if($distribution->is_repaid)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i> Repaid
                                                    </span>
                                                @elseif($distribution->due_date && $distribution->due_date->isPast())
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> Overdue
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i> Pending
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($distribution->season)
                                                {{ $distribution->season->name }} {{ $distribution->season->year }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('stock.distributions.show', $distribution) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <h5>No distributions found</h5>
                                                <p>Start by creating a new distribution to farmers</p>
                                                <a href="{{ route('stock.distributions.create') }}" class="btn btn-success">
                                                    <i class="fas fa-plus-circle me-1"></i> New Distribution
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($distributions->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $distributions->firstItem() }} to {{ $distributions->lastItem() }} of {{ $distributions->total() }} distributions
                                </div>
                                <div>
                                    {{ $distributions->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
