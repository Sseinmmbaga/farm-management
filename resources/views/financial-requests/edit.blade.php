@extends('layouts.base')

@section('title', 'Edit Financial Request #' . $financialRequest->request_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Financial Request #{{ $financialRequest->request_number }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('financial-requests.show', $financialRequest) }}" class="btn btn-light">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                            <a href="{{ route('financial-requests.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('financial-requests.update', $financialRequest) }}" id="financialRequestForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-select" required {{ $financialRequest->is_editable ? '' : 'disabled' }}>
                                        <option value="">Select Employee</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $financialRequest->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(!$financialRequest->is_editable)
                                        <input type="hidden" name="user_id" value="{{ $financialRequest->user_id }}">
                                        <small class="text-muted">Employee cannot be changed after approval.</small>
                                    @endif
                                    @error('user_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Request Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select" required {{ $financialRequest->is_editable ? '' : 'disabled' }}>
                                        <option value="">Select Type</option>
                                        @foreach($requestTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $financialRequest->type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(!$financialRequest->is_editable)
                                        <input type="hidden" name="type" value="{{ $financialRequest->type }}">
                                        <small class="text-muted">Type cannot be changed after approval.</small>
                                    @endif
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                            value="{{ old('amount', $financialRequest->amount) }}" step="0.01" min="1000" max="100000000" required {{ $financialRequest->is_editable ? '' : 'disabled' }}>
                                        <select class="form-select" id="currency" name="currency" style="max-width: 120px;" {{ $financialRequest->is_editable ? '' : 'disabled' }}>
                                            @foreach($currencies as $key => $label)
                                                <option value="{{ $key }}" {{ old('currency', $financialRequest->currency) == $key ? 'selected' : '' }}>
                                                    {{ $key }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if(!$financialRequest->is_editable)
                                        <input type="hidden" name="amount" value="{{ $financialRequest->amount }}">
                                        <input type="hidden" name="currency" value="{{ $financialRequest->currency }}">
                                        <small class="text-muted">Amount cannot be changed after approval.</small>
                                    @endif
                                    <small class="text-muted">Enter amount between 1,000 and 100,000,000.</small>
                                    @error('amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    @error('currency')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="repayment_installments" class="form-label">Repayment Installments (Optional)</label>
                                    <input type="number" class="form-control" id="repayment_installments" name="repayment_installments" 
                                        value="{{ old('repayment_installments', $financialRequest->repayment_installments) }}" min="1" max="60" step="1" {{ $financialRequest->is_editable ? '' : 'disabled' }}>
                                    @if(!$financialRequest->is_editable)
                                        <input type="hidden" name="repayment_installments" value="{{ $financialRequest->repayment_installments }}">
                                        <small class="text-muted">Installments cannot be changed after approval.</small>
                                    @endif
                                    <small class="text-muted">Number of installments for loan repayment (1-60).</small>
                                    @error('repayment_installments')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="purpose" name="purpose" rows="4" required {{ $financialRequest->is_editable ? '' : 'disabled' }}>{{ old('purpose', $financialRequest->purpose) }}</textarea>
                            @if(!$financialRequest->is_editable)
                                <input type="hidden" name="purpose" value="{{ $financialRequest->purpose }}">
                                <small class="text-muted">Purpose cannot be changed after approval.</small>
                            @endif
                            <small class="text-muted">Provide a detailed purpose for this financial request (10-1000 characters).</small>
                            @error('purpose')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="repayment_start_date" class="form-label">Repayment Start Date (Optional)</label>
                                    <input type="date" class="form-control" id="repayment_start_date" name="repayment_start_date" 
                                        value="{{ old('repayment_start_date', $financialRequest->repayment_start_date ? $financialRequest->repayment_start_date->format('Y-m-d') : '') }}" 
                                        min="{{ date('Y-m-d') }}" {{ $financialRequest->is_editable ? '' : 'disabled' }}>
                                    @if(!$financialRequest->is_editable)
                                        <input type="hidden" name="repayment_start_date" value="{{ $financialRequest->repayment_start_date ? $financialRequest->repayment_start_date->format('Y-m-d') : '' }}">
                                        <small class="text-muted">Repayment start date cannot be changed after approval.</small>
                                    @endif
                                    <small class="text-muted">If left blank, start date will be set after approval.</small>
                                    @error('repayment_start_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2" {{ $financialRequest->is_editable ? '' : 'disabled' }}>{{ old('notes', $financialRequest->notes) }}</textarea>
                                    @if(!$financialRequest->is_editable)
                                        <input type="hidden" name="notes" value="{{ $financialRequest->notes }}">
                                        <small class="text-muted">Notes cannot be changed after approval.</small>
                                    @endif
                                    @error('notes')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> Financial request details cannot be changed after approval (except for notes). 
                            If you need to make changes after approval, please cancel the request and create a new one.
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Current Status:</strong> 
                            <span class="badge bg-{{ $financialRequest->status_color }}">
                                {{ $financialRequest->status_display }}
                            </span>
                            @if($financialRequest->approved_at)
                                <br>Approved by {{ $financialRequest->approvedBy->name ?? 'N/A' }} on {{ $financialRequest->approved_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            @if($financialRequest->is_editable)
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Update Financial Request
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban me-1"></i> Cannot Edit (Already Approved)
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure repayment start date is not before today
        const startDateInput = document.getElementById('repayment_start_date');
        if (startDateInput && !startDateInput.disabled) {
            startDateInput.min = new Date().toISOString().split('T')[0];
        }
    });
</script>
@endpush