@extends('layouts.base')

@section('title', 'Create Financial Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i> Create Financial Request
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('financial-requests.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('financial-requests.store') }}" id="financialRequestForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-select" required>
                                        <option value="">Select Employee</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Request Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        @foreach($requestTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                            value="{{ old('amount') }}" step="0.01" min="1000" max="100000000" required>
                                        <select class="form-select" id="currency" name="currency" style="max-width: 120px;">
                                            @foreach($currencies as $key => $label)
                                                <option value="{{ $key }}" {{ old('currency', 'TZS') == $key ? 'selected' : '' }}>
                                                    {{ $key }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                        value="{{ old('repayment_installments') }}" min="1" max="60" step="1">
                                    <small class="text-muted">Number of installments for loan repayment (1-60).</small>
                                    @error('repayment_installments')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="purpose" name="purpose" rows="4" required>{{ old('purpose') }}</textarea>
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
                                        value="{{ old('repayment_start_date') }}" min="{{ date('Y-m-d') }}">
                                    <small class="text-muted">If left blank, start date will be set after approval.</small>
                                    @error('repayment_start_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Financial requests will be submitted for approval. You can track the status from the list.
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Submit Financial Request
                            </button>
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
        if (startDateInput) {
            startDateInput.min = new Date().toISOString().split('T')[0];
        }
    });
</script>
@endpush