@extends('layouts.base')

@section('title', 'Farmer Forms')

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">Farmer Forms</h4>
        <small class="text-muted">Manage and submit farmer data collection forms</small>
    </div>
    <a href="{{ route('farmer-forms.select-farmer') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>New Form
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('farmer-forms.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Form Type</label>
                    <select name="form_type" class="form-select">
                        <option value="">All Forms</option>
                        @foreach($accessibleForms as $type => $info)
                            <option value="{{ $type }}" {{ request('form_type') == $type ? 'selected' : '' }}>
                                {{ $info['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search Farmer</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or registration number..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('farmer-forms.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Forms Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Form Type</th>
                        <th>Farmer</th>
                        <th>Season</th>
                        <th>Date</th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($forms as $form)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $form->form_type_label }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $form->form_type_swahili }}</small>
                                </div>
                            </td>
                            <td>
                                @if($form->farmer)
                                    <a href="{{ route('farmers.show', $form->farmer) }}">
                                        {{ $form->farmer->first_name }} {{ $form->farmer->last_name }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $form->farmer->registration_number }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $form->season ?? '-' }}</td>
                            <td>{{ $form->form_date?->format('d M Y') ?? $form->created_at->format('d M Y') }}</td>
                            <td>{{ $form->submittedBy->name ?? 'Unknown' }}</td>
                            <td>
                                <span class="badge {{ $form->status_badge_class }}">
                                    {{ ucfirst($form->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('farmer-forms.show', $form) }}" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($form->canBeEdited() && ($form->submitted_by == auth()->id() || auth()->user()->isAdmin() || auth()->user()->isSupervisor()))
                                        <a href="{{ route('farmer-forms.edit', $form) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('farmer-forms.print', $form) }}" class="btn btn-outline-secondary" title="Print" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-file-alt fa-3x mb-3 d-block"></i>
                                    No forms found. <a href="{{ route('farmer-forms.select-farmer') }}">Create your first form</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($forms->hasPages())
        <div class="card-footer">
            {{ $forms->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
