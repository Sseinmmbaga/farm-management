@extends('layouts.base')

@section('title', 'My Certificates')

@push('styles')
<style>
    .certificate-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #eee;
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .certificate-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .certificate-icon {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 20px;
    }
    .icon-organic { background-color: rgba(39, 174, 96, 0.1); color: #27ae60; }
    .icon-training { background-color: rgba(52, 152, 219, 0.1); color: #3498db; }
    .icon-safety { background-color: rgba(243, 156, 18, 0.1); color: #f39c12; }
    .icon-quality { background-color: rgba(155, 89, 182, 0.1); color: #9b59b6; }
    .certificate-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    .badge-valid, .badge-active { background-color: #e6f7ee; color: #27ae60; }
    .badge-expired { background-color: #ffeaea; color: #c0392b; }
    .badge-pending, .badge-expiring { background-color: #fff4e6; color: #e67e22; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">My Certificates</h1>
            <p class="text-muted mb-0">View and manage your training and quality certificates</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-success mb-1">{{ $stats['valid'] }}</h2>
                    <p class="text-muted mb-0">Valid Certificates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-warning mb-1">{{ $stats['pending_renewal'] }}</h2>
                    <p class="text-muted mb-0">Pending Renewal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-danger mb-1">{{ $stats['expired'] }}</h2>
                    <p class="text-muted mb-0">Expired</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="text-info mb-1">{{ $stats['total'] }}</h2>
                    <p class="text-muted mb-0">Total Certificates</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Training Certificates -->
    @if($trainingCertificates->count() > 0)
    <h5 class="mb-3"><i class="fas fa-chalkboard-teacher me-2"></i>Training Certificates</h5>
    <div class="row mb-4">
        @foreach($trainingCertificates as $cert)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="certificate-card">
                <div class="certificate-icon icon-training">
                    <i class="fas fa-award"></i>
                </div>
                <h5 class="mb-2">{{ $cert->program?->name ?? 'Training Certificate' }}</h5>
                <p class="text-muted small mb-2">{{ $cert->session?->title ?? 'Training Session' }}</p>
                <p class="small mb-3">
                    Issued: {{ $cert->issue_date?->format('M d, Y') ?? 'N/A' }}<br>
                    @if($cert->expiry_date)
                    Expires: {{ $cert->expiry_date->format('M d, Y') }}
                    @else
                    No Expiry
                    @endif
                </p>
                <p class="small text-muted mb-3">
                    Certificate #: {{ $cert->certificate_number ?? 'N/A' }}
                </p>
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="certificate-badge badge-{{ $cert->status }}">
                            {{ ucfirst($cert->status) }}
                        </span>
                        <div class="d-flex gap-2">
                            @if($cert->file_path)
                            <a href="{{ Storage::url($cert->file_path) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Document Certificates -->
    @if($documentCertificates->count() > 0)
    <h5 class="mb-3"><i class="fas fa-certificate me-2"></i>Other Certificates</h5>
    <div class="row mb-4">
        @foreach($documentCertificates as $doc)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="certificate-card">
                <div class="certificate-icon icon-{{ $doc->type === 'organic_certificate' ? 'organic' : 'quality' }}">
                    <i class="fas {{ $doc->type === 'organic_certificate' ? 'fa-leaf' : 'fa-file-alt' }}"></i>
                </div>
                <h5 class="mb-2">{{ $doc->title ?? $doc->type_label }}</h5>
                <p class="text-muted small mb-2">{{ $doc->type_label }}</p>
                <p class="small mb-3">
                    Issued: {{ $doc->issue_date?->format('M d, Y') ?? 'N/A' }}<br>
                    @if($doc->expiry_date)
                    Expires: {{ $doc->expiry_date->format('M d, Y') }}
                    @else
                    No Expiry
                    @endif
                </p>
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        @if($doc->is_expired)
                        <span class="certificate-badge badge-expired">Expired</span>
                        @elseif($doc->is_expiring)
                        <span class="certificate-badge badge-expiring">Expiring Soon</span>
                        @else
                        <span class="certificate-badge badge-valid">Valid</span>
                        @endif
                        <div class="d-flex gap-2">
                            @if($doc->file_path)
                            <a href="{{ Storage::url($doc->file_path) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($trainingCertificates->isEmpty() && $documentCertificates->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-certificate fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">No Certificates Yet</h5>
        <p class="text-muted">You haven't earned any certificates yet. Complete trainings to get certified.</p>
        <a href="{{ route('dashboard.farmer.trainings') }}" class="btn btn-primary">
            <i class="fas fa-chalkboard-teacher"></i> Browse Trainings
        </a>
    </div>
    @endif

    <!-- Renewal Reminder -->
    @if($stats['pending_renewal'] > 0)
    <div class="card border-warning mt-4">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0"><i class="fas fa-exclamation-circle"></i> Renewal Reminders</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">You have <strong>{{ $stats['pending_renewal'] }} certificate(s)</strong> that need renewal within the next 60 days. Please complete the renewal process to avoid interruption.</p>
        </div>
    </div>
    @endif
@endsection
