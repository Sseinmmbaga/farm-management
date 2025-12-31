@extends('layouts.base')

@section('title', 'Certificate Details - ' . $certificate->certificate_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-certificate me-2"></i>
                            Certificate Details
                        </h4>
                        <div>
                            <a href="{{ route('training.certificates.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                            <a href="{{ route('training.certificates.download', $certificate) }}" class="btn btn-light">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Certificate Preview -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card border-success">
                                <div class="card-body text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-award fa-5x text-success"></i>
                                    </div>
                                    <h1 class="display-5 text-success mb-2">Certificate of Completion</h1>
                                    <p class="lead">This certifies that</p>
                                    <h2 class="mb-3">
                                        <strong>{{ $certificate->farmer->first_name }} {{ $certificate->farmer->last_name }}</strong>
                                    </h2>
                                    <p class="mb-3">has successfully completed the training program</p>
                                    <h3 class="text-primary">{{ $certificate->program->name }}</h3>
                                    <p class="mb-4">
                                        on {{ $certificate->session->scheduled_date->format('F j, Y') }} at {{ $certificate->session->venue }}
                                    </p>
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <p><strong>Certificate Number:</strong><br>{{ $certificate->certificate_number }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Issue Date:</strong><br>{{ $certificate->issue_date->format('F j, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-top">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="signature">
                                                    <p class="mb-1">______________________</p>
                                                    <small>Training Coordinator</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="signature">
                                                    <p class="mb-1">______________________</p>
                                                    <small>Quality Assurance</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="signature">
                                                    <p class="mb-1">______________________</p>
                                                    <small>Issued By</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Details -->
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Certificate Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th>Certificate Number</th>
                                                <td>{{ $certificate->certificate_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge bg-{{ $certificate->status == 'active' ? 'success' : ($certificate->status == 'expired' ? 'warning' : 'danger') }}">
                                                        {{ $certificate->status_label }}
                                                    </span>
                                                    @if($certificate->is_valid)
                                                        <span class="badge bg-success">Valid</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Issue Date</th>
                                                <td>{{ $certificate->issue_date->format('M d, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Expiry Date</th>
                                                <td>
                                                    @if($certificate->expiry_date)
                                                        {{ $certificate->expiry_date->format('M d, Y') }}
                                                        <br>
                                                        <small class="text-muted">
                                                            @if($certificate->days_to_expiry !== null)
                                                                {{ abs($certificate->days_to_expiry) }} days {{ $certificate->days_to_expiry < 0 ? 'ago' : 'left' }}
                                                            @endif
                                                        </small>
                                                    @else
                                                        <span class="text-muted">No expiry</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Farmer</th>
                                                <td>
                                                    <strong>{{ $certificate->farmer->first_name }} {{ $certificate->farmer->last_name }}</strong><br>
                                                    <small class="text-muted">{{ $certificate->farmer->registration_number }}</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Program</th>
                                                <td>
                                                    <strong>{{ $certificate->program->name }}</strong><br>
                                                    <small class="text-muted">{{ $certificate->program->code }}</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Session</th>
                                                <td>
                                                    <strong>{{ $certificate->session->title }}</strong><br>
                                                    <small class="text-muted">{{ $certificate->session->scheduled_date->format('M d, Y') }}</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Issued By</th>
                                                <td>{{ $certificate->issuedBy->name ?? 'System' }}</td>
                                            </tr>
                                            <tr>
                                                <th>File</th>
                                                <td>
                                                    @if($certificate->file_path)
                                                        <a href="{{ $certificate->certificate_url }}" target="_blank" class="btn btn-sm btn-success">
                                                            <i class="fas fa-external-link-alt"></i> View PDF
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No file attached</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card border-warning mt-3">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0">Certificate Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('training.certificates.download', $certificate) }}" class="btn btn-success">
                                            <i class="fas fa-download me-1"></i> Download Certificate
                                        </a>
                                        @if($certificate->status == 'active')
                                            <button type="button"
                                                    class="btn btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#revokeModal">
                                                <i class="fas fa-ban me-1"></i> Revoke Certificate
                                            </button>
                                        @endif
                                        @if($certificate->status == 'revoked')
                                            <button type="button"
                                                    class="btn btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reactivateModal">
                                                <i class="fas fa-redo me-1"></i> Reactivate
                                            </button>
                                        @endif
                                        <a href="{{ route('training.reports.farmer', $certificate->farmer) }}" class="btn btn-info">
                                            <i class="fas fa-user-graduate me-1"></i> Farmer Training History
                                        </a>
                                        <a href="{{ route('training.sessions.show', $certificate->session) }}" class="btn btn-secondary">
                                            <i class="fas fa-calendar-alt me-1"></i> View Session
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revoke Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('training.certificates.revoke', $certificate) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Revoke Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to revoke this certificate? This action cannot be undone.</p>
                    <div class="mb-3">
                        <label for="revocation_reason" class="form-label">Revocation Reason</label>
                        <textarea class="form-control" id="revocation_reason" name="revocation_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Revoke Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reactivate Modal -->
<div class="modal fade" id="reactivateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('training.certificates.reactivate', $certificate) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reactivate Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reactivate this certificate?</p>
                    <div class="mb-3">
                        <label for="reactivation_reason" class="form-label">Reactivation Reason</label>
                        <textarea class="form-control" id="reactivation_reason" name="reactivation_reason" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reactivate Certificate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection