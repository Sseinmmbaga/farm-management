@extends('layouts.base')

@section('title', 'Create Leave Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i> Create Leave Request
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('leave-requests.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('leave-requests.store') }}" enctype="multipart/form-data" id="leaveRequestForm">
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
                                    <label for="type" class="form-label">Leave Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        @foreach($leaveTypes as $key => $label)
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
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                        value="{{ old('start_date') }}" 
                                        min="{{ date('Y-m-d') }}" required>
                                    <small class="text-muted">Leave cannot start before today.</small>
                                    @error('start_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                        value="{{ old('end_date') }}" 
                                        min="{{ date('Y-m-d') }}" required>
                                    <small class="text-muted">Must be on or after start date.</small>
                                    @error('end_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>
                            <small class="text-muted">Provide a detailed reason for the leave request.</small>
                            @error('reason')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_sick_sheet" name="is_sick_sheet" value="1" {{ old('is_sick_sheet') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_sick_sheet">This is a sick leave with a medical certificate</label>
                                    </div>
                                    @error('is_sick_sheet')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sick_sheet_file" class="form-label">Medical Certificate (Optional)</label>
                                    <input type="file" class="form-control" id="sick_sheet_file" name="sick_sheet_file" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Upload a scanned copy of the medical certificate (max 5MB).</small>
                                    @error('sick_sheet_file')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Leave requests will be submitted for approval. You can track the status from the list.
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Submit Leave Request
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
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        // Set min for end date based on start date
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
        });

        // Validate end date not before start date
        endDate.addEventListener('change', function() {
            if (this.value < startDate.value) {
                alert('End date cannot be before start date.');
                this.value = startDate.value;
            }
        });
    });
</script>
@endpush