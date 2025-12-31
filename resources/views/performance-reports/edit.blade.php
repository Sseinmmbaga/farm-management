@extends('layouts.app')

@section('title', 'Edit Performance Report - ' . $performanceReport->report_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        Edit Performance Report
                        <span class="badge bg-{{ $performanceReport->status_color }} ms-2">
                            {{ $performanceReport->status_display }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('performance-reports.update', $performanceReport) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Employee -->
                            <div class="col-md-6">
                                <label for="user_id" class="form-label required">Employee</label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select Employee</option>
                                    @foreach($users as $id => $name)
                                        <option value="{{ $id }}" {{ old('user_id', $performanceReport->user_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="col-md-6">
                                <label for="department_id" class="form-label required">Department</label>
                                <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $id => $name)
                                        <option value="{{ $id }}" {{ old('department_id', $performanceReport->department_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Period Type -->
                            <div class="col-md-4">
                                <label for="period_type" class="form-label required">Period Type</label>
                                <select name="period_type" id="period_type" class="form-select @error('period_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    @foreach($periodTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('period_type', $performanceReport->period_type) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('period_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Year -->
                            <div class="col-md-4">
                                <label for="period_year" class="form-label required">Year</label>
                                <input type="number" name="period_year" id="period_year"
                                       class="form-control @error('period_year') is-invalid @enderror"
                                       min="2020" max="2100" step="1"
                                       value="{{ old('period_year', $performanceReport->period_year) }}" required>
                                @error('period_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Month (for monthly/quarterly) -->
                            <div class="col-md-4">
                                <label for="period_month" class="form-label">Month (for monthly/quarterly)</label>
                                <input type="number" name="period_month" id="period_month"
                                       class="form-control @error('period_month') is-invalid @enderror"
                                       min="1" max="12" step="1"
                                       value="{{ old('period_month', $performanceReport->period_month) }}">
                                <small class="text-muted">Leave empty for yearly reports.</small>
                                @error('period_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Report Date -->
                            <div class="col-md-6">
                                <label for="report_date" class="form-label required">Report Date</label>
                                <input type="date" name="report_date" id="report_date"
                                       class="form-control @error('report_date') is-invalid @enderror"
                                       value="{{ old('report_date', $performanceReport->report_date->format('Y-m-d')) }}" required>
                                @error('report_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Summary -->
                            <div class="col-12">
                                <label for="summary" class="form-label required">Summary</label>
                                <textarea name="summary" id="summary" rows="4"
                                          class="form-control @error('summary') is-invalid @enderror"
                                          placeholder="Overall performance summary..."
                                          required>{{ old('summary', $performanceReport->summary) }}</textarea>
                                @error('summary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Strengths -->
                            <div class="col-md-6">
                                <label for="strengths" class="form-label">Strengths</label>
                                <textarea name="strengths" id="strengths" rows="3"
                                          class="form-control @error('strengths') is-invalid @enderror"
                                          placeholder="Key strengths...">{{ old('strengths', $performanceReport->strengths) }}</textarea>
                                @error('strengths')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Areas for Improvement -->
                            <div class="col-md-6">
                                <label for="improvements" class="form-label">Areas for Improvement</label>
                                <textarea name="improvements" id="improvements" rows="3"
                                          class="form-control @error('improvements') is-invalid @enderror"
                                          placeholder="Areas needing improvement...">{{ old('improvements', $performanceReport->improvements) }}</textarea>
                                @error('improvements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Recommendations -->
                            <div class="col-12">
                                <label for="recommendations" class="form-label">Recommendations</label>
                                <textarea name="recommendations" id="recommendations" rows="3"
                                          class="form-control @error('recommendations') is-invalid @enderror"
                                          placeholder="Recommendations for future performance...">{{ old('recommendations', $performanceReport->recommendations) }}</textarea>
                                @error('recommendations')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea name="notes" id="notes" rows="2"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Any additional notes...">{{ old('notes', $performanceReport->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Metrics (optional) -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Performance Metrics</h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($performanceReport->getMetricsWithLabels() as $metric)
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ $metric['label'] }}</label>
                                                    <input type="hidden" name="metrics[{{ $metric['key'] }}][label]" value="{{ $metric['label'] }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Score (0‑100)</label>
                                                    <input type="number" name="metrics[{{ $metric['key'] }}][score]"
                                                           class="form-control" min="0" max="100" step="0.1"
                                                           value="{{ old('metrics.' . $metric['key'] . '.score', $metric['score']) }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Comments</label>
                                                    <input type="text" name="metrics[{{ $metric['key'] }}][comments]"
                                                           class="form-control"
                                                           value="{{ old('metrics.' . $metric['key'] . '.comments', $metric['comments']) }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('performance-reports.show', $performanceReport) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <div class="btn-group">
                                        <button type="submit" name="action" value="save" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                        @if($performanceReport->canBeSubmitted())
                                            <button type="submit" name="action" value="submit" class="btn btn-success">
                                                <i class="fas fa-paper-plane"></i> Update & Submit
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#user_id, #department_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select...'
        });
    });
</script>
@endpush