<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Labor Records - Remei Farm OS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
        }

        .navbar {
            background-color: #27ae60;
            margin-bottom: 20px;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }

        .stats-card {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-card .stat-item {
            text-align: center;
            padding: 10px;
            border-right: 1px solid rgba(255,255,255,0.2);
        }

        .stats-card .stat-item:last-child {
            border-right: none;
        }

        .stats-card .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stats-card .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .filter-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .table-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .labor-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #27ae60;
        }

        .labor-table tr:hover {
            background-color: #f8f9fa;
        }

        .worker-info {
            display: flex;
            align-items: center;
        }

        .worker-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #27ae60;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-right: 10px;
        }

        .bulk-actions {
            display: none;
            background-color: #e9ecef;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .bulk-actions.show {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .amount-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-seedling"></i> Remei Farm OS
            </a>
            <div class="navbar-nav ms-auto">
                @auth
                    <a class="nav-link" href="/dashboard">Dashboard</a>
                    <a class="nav-link" href="{{ route('tasks.index') }}">Tasks</a>
                    <a class="nav-link active" href="{{ route('labor.index') }}">Labor</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-clock-history"></i> Labor Records</h1>
            <div>
                @can('export', App\Models\Tasks\Labor::class)
                    <a href="{{ route('labor.export', request()->all()) }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-download"></i> Export
                    </a>
                @endcan
                @can('create', App\Models\Tasks\Labor::class)
                    <a href="{{ route('labor.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Log Labor
                    </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Overview -->
        <div class="stats-card">
            <div class="row">
                <div class="col-md-3 stat-item">
                    <div class="stat-number">{{ number_format($stats['total_hours'], 1) }}</div>
                    <div class="stat-label">Total Hours</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">{{ number_format($stats['total_cost']) }}</div>
                    <div class="stat-label">Total Cost (TZS)</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">{{ number_format($stats['pending_payment']) }}</div>
                    <div class="stat-label">Pending Payment (TZS)</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">{{ $stats['records_count'] }}</div>
                    <div class="stat-label">Records</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('labor.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Code, name...">
                </div>

                <div class="col-md-2">
                    <label for="labor_type" class="form-label">Labor Type</label>
                    <select class="form-select" id="labor_type" name="labor_type">
                        <option value="">All Types</option>
                        @foreach($laborTypes as $type)
                            <option value="{{ $type->value }}" {{ request('labor_type') == $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('payment_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="farm_id" class="form-label">Farm</label>
                    <select class="form-select" id="farm_id" name="farm_id">
                        <option value="">All Farms</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from"
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to"
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'labor_type', 'payment_status', 'farm_id', 'date_from', 'date_to', 'task_id']))
                        <a href="{{ route('labor.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions">
            <div>
                <span id="selectedCount">0</span> records selected
            </div>
            <div>
                @can('approve', App\Models\Tasks\Labor::class)
                    <form action="{{ route('labor.bulk-approve') }}" method="POST" class="d-inline" id="bulkApproveForm">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-info">
                            <i class="bi bi-check-circle"></i> Approve Selected
                        </button>
                    </form>
                @endcan
                @can('markAsPaid', App\Models\Tasks\Labor::class)
                    <form action="{{ route('labor.bulk-pay') }}" method="POST" class="d-inline" id="bulkPayForm">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-cash"></i> Mark as Paid
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- Labor Records Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table labor-table">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Code</th>
                            <th>Worker</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Task/Farm</th>
                            <th>Hours</th>
                            <th class="text-end">Cost (TZS)</th>
                            <th>Payment</th>
                            <th>Verified</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laborRecords as $labor)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input record-checkbox"
                                           value="{{ $labor->id }}" data-status="{{ $labor->payment_status }}">
                                </td>
                                <td>
                                    <a href="{{ route('labor.show', $labor) }}" class="text-decoration-none">
                                        {{ $labor->code }}
                                    </a>
                                </td>
                                <td>
                                    <div class="worker-info">
                                        <div class="worker-avatar">
                                            {{ strtoupper(substr($labor->worker_display_name, 0, 1)) }}
                                        </div>
                                        <span>{{ Str::limit($labor->worker_display_name, 20) }}</span>
                                    </div>
                                </td>
                                <td>{{ $labor->work_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $labor->labor_type_color }}">
                                        {{ $labor->labor_type_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($labor->task)
                                        <a href="{{ route('tasks.show', $labor->task) }}" class="text-decoration-none">
                                            {{ Str::limit($labor->task->title, 20) }}
                                        </a>
                                    @elseif($labor->farm)
                                        {{ Str::limit($labor->farm->name, 20) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $labor->work_duration }}</td>
                                <td class="text-end amount-cell">{{ number_format($labor->total_cost) }}</td>
                                <td>
                                    <span class="badge bg-{{ $labor->payment_status_color }}">
                                        {{ $labor->payment_status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($labor->is_verified)
                                        <i class="bi bi-check-circle text-success"></i>
                                    @else
                                        <i class="bi bi-x-circle text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('labor.show', $labor) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('update', $labor)
                                            <a href="{{ route('labor.edit', $labor) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No labor records found.</p>
                                    @can('create', App\Models\Tasks\Labor::class)
                                        <a href="{{ route('labor.create') }}" class="btn btn-sm btn-success mt-2">
                                            <i class="bi bi-plus"></i> Log First Record
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($laborRecords->count() > 0)
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="6" class="text-end">Page Total:</th>
                                <th>{{ $laborRecords->sum('hours_worked') }} hrs</th>
                                <th class="text-end amount-cell">{{ number_format($laborRecords->sum('total_cost')) }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Pagination -->
            @if($laborRecords->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $laborRecords->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.record-checkbox');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            const bulkApproveForm = document.getElementById('bulkApproveForm');
            const bulkPayForm = document.getElementById('bulkPayForm');

            // Select all functionality
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            // Individual checkbox change
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            function updateBulkActions() {
                const selected = document.querySelectorAll('.record-checkbox:checked');
                const count = selected.length;

                selectedCount.textContent = count;

                if (count > 0) {
                    bulkActions.classList.add('show');
                } else {
                    bulkActions.classList.remove('show');
                }

                // Update hidden inputs in bulk forms
                updateFormInputs(bulkApproveForm, selected);
                updateFormInputs(bulkPayForm, selected);
            }

            function updateFormInputs(form, selected) {
                if (!form) return;

                // Remove existing hidden inputs
                form.querySelectorAll('input[name="labor_ids[]"]').forEach(input => input.remove());

                // Add new hidden inputs
                selected.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'labor_ids[]';
                    input.value = checkbox.value;
                    form.appendChild(input);
                });
            }
        });
    </script>
</body>
</html>
