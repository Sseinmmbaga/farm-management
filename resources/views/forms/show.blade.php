@extends('layouts.base')

@section('title', $farmerForm->form_type_label)

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $farmerForm->form_type_label }}</h4>
        <small class="text-muted">{{ $farmerForm->form_type_swahili }}</small>
    </div>
    <div class="btn-group">
        <a href="{{ route('farmer-forms.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        <a href="{{ route('farmer-forms.print', $farmerForm) }}" class="btn btn-outline-primary" target="_blank">
            <i class="fas fa-print me-2"></i>Print
        </a>
        @if($farmerForm->canBeEdited() && ($farmerForm->submitted_by == auth()->id() || auth()->user()->isAdmin() || auth()->user()->isSupervisor()))
            <a href="{{ route('farmer-forms.edit', $farmerForm) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        @endif
    </div>
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

<div class="row">
    <!-- Form Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Form Data</h5>
                <span class="badge {{ $farmerForm->status_badge_class }} fs-6">{{ ucfirst($farmerForm->status) }}</span>
            </div>
            <div class="card-body">
                @if($farmerForm->farmer)
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6><i class="fas fa-user me-2"></i>Farmer Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Name:</strong> {{ $farmerForm->farmer->first_name }} {{ $farmerForm->farmer->last_name }}</p>
                                <p class="mb-1"><strong>Reg. Number:</strong> {{ $farmerForm->farmer->registration_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Village:</strong> {{ $farmerForm->farmer->village?->name ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Phone:</strong> {{ $farmerForm->farmer->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($farmerForm->farm)
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6><i class="fas fa-tractor me-2"></i>Farm Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Farm Name:</strong> {{ $farmerForm->farm->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Size:</strong> {{ $farmerForm->farm->size }} acres</p>
                            </div>
                        </div>
                    </div>
                @endif

                <h6 class="mb-3"><i class="fas fa-clipboard-list me-2"></i>Form Responses</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($farmerForm->form_data as $key => $value)
                                <tr>
                                    <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                    <td>
                                        @if(is_array($value))
                                            {{ implode(', ', $value) }}
                                        @elseif(is_bool($value))
                                            {{ $value ? 'Yes' : 'No' }}
                                        @else
                                            {{ $value ?: '-' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Review Section (for supervisors/admins) -->
        @if((auth()->user()->isAdmin() || auth()->user()->isSupervisor()) && $farmerForm->canBeReviewed())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Review Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('farmer-forms.review', $farmerForm) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Reviewer Notes</label>
                            <textarea name="reviewer_notes" class="form-control" rows="3" placeholder="Add any notes or comments..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Approve
                            </button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger">
                                <i class="fas fa-times me-2"></i>Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Reviewer Notes -->
        @if($farmerForm->reviewer_notes)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Reviewer Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $farmerForm->reviewer_notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Form Information</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Form ID:</dt>
                    <dd class="col-sm-7">#{{ $farmerForm->id }}</dd>

                    <dt class="col-sm-5">Season:</dt>
                    <dd class="col-sm-7">{{ $farmerForm->season ?? '-' }}</dd>

                    <dt class="col-sm-5">Form Date:</dt>
                    <dd class="col-sm-7">{{ $farmerForm->form_date?->format('d M Y') ?? '-' }}</dd>

                    <dt class="col-sm-5">Created:</dt>
                    <dd class="col-sm-7">{{ $farmerForm->created_at->format('d M Y H:i') }}</dd>

                    <dt class="col-sm-5">Updated:</dt>
                    <dd class="col-sm-7">{{ $farmerForm->updated_at->format('d M Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Submission Details</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Submitted By:</dt>
                    <dd class="col-sm-7">{{ $farmerForm->submittedBy?->name ?? 'Unknown' }}</dd>

                    @if($farmerForm->reviewedBy)
                        <dt class="col-sm-5">Reviewed By:</dt>
                        <dd class="col-sm-7">{{ $farmerForm->reviewedBy->name }}</dd>
                    @endif

                    <dt class="col-sm-5">Status:</dt>
                    <dd class="col-sm-7">
                        <span class="badge {{ $farmerForm->status_badge_class }}">
                            {{ ucfirst($farmerForm->status) }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>

        @if($farmerForm->status === 'draft' && $farmerForm->submitted_by == auth()->id())
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-trash me-2"></i>Delete Form</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">This form is still in draft status and can be deleted.</p>
                    <form action="{{ route('farmer-forms.destroy', $farmerForm) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this form?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash me-2"></i>Delete Form
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
