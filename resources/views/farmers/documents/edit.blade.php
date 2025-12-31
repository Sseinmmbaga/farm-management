@extends('layouts.base')

@section('title', 'Edit Document')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file-edit me-2"></i> Edit Document for {{ $farmer->full_name }}
                        </h4>
                        <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Farmer
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('farmers.documents.update', [$farmer, $document]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Document Type *</label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            required>
                                        <option value="">Select Type</option>
                                        @foreach($documentTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $document->type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Document Title *</label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $document->title) }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="issue_date" class="form-label">Issue Date</label>
                                    <input type="date"
                                           class="form-control @error('issue_date') is-invalid @enderror"
                                           id="issue_date"
                                           name="issue_date"
                                           value="{{ old('issue_date', $document->issue_date?->format('Y-m-d')) }}">
                                    @error('issue_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date"
                                           class="form-control @error('expiry_date') is-invalid @enderror"
                                           id="expiry_date"
                                           name="expiry_date"
                                           value="{{ old('expiry_date', $document->expiry_date?->format('Y-m-d')) }}">
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">Document File</label>
                            <input type="file"
                                   class="form-control @error('file') is-invalid @enderror"
                                   id="file"
                                   name="file"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">Accepted formats: PDF, JPG, PNG, DOC, DOCX. Max size: 10MB. Leave empty to keep existing file.</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($document->file_path)
                                <div class="mt-2">
                                    <p class="mb-1">Current file:</p>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file me-2 text-primary"></i>
                                        <span>{{ basename($document->file_path) }} ({{ $document->file_size_formatted }})</span>
                                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-3">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        <a href="{{ Storage::url($document->file_path) }}" download class="btn btn-sm btn-outline-success ms-2">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes"
                                      name="notes"
                                      rows="3">{{ old('notes', $document->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Optional: Validate expiry date after issue date
    document.getElementById('issue_date').addEventListener('change', function() {
        const expiryInput = document.getElementById('expiry_date');
        if (this.value && expiryInput.value && expiryInput.value < this.value) {
            expiryInput.setCustomValidity('Expiry date must be on or after issue date.');
        } else {
            expiryInput.setCustomValidity('');
        }
    });

    document.getElementById('expiry_date').addEventListener('change', function() {
        const issueInput = document.getElementById('issue_date');
        if (issueInput.value && this.value && this.value < issueInput.value) {
            this.setCustomValidity('Expiry date must be on or after issue date.');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
@endpush
@endsection