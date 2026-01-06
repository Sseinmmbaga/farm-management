@extends('layouts.base')

@section('title', 'Select Form')

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">Select Form Type</h4>
        <small class="text-muted">
            Filling form for: <strong>{{ $farmer->first_name }} {{ $farmer->last_name }}</strong>
            ({{ $farmer->registration_number ?? 'No Reg #' }})
        </small>
    </div>
    <a href="{{ route('farmer-forms.select-farmer') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Change Farmer
    </a>
</div>

<div class="row g-4">
    @foreach($accessibleForms as $type => $info)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm form-card">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="form-icon me-3">
                            @switch($type)
                                @case('form1')
                                    <i class="fas fa-id-card fa-2x text-primary"></i>
                                    @break
                                @case('form2')
                                @case('form3')
                                    <i class="fas fa-file-alt fa-2x text-success"></i>
                                    @break
                                @case('form4')
                                    <i class="fas fa-clipboard-list fa-2x text-info"></i>
                                    @break
                                @case('form5')
                                    <i class="fas fa-check-double fa-2x text-warning"></i>
                                    @break
                                @case('form6')
                                    <i class="fas fa-chart-line fa-2x text-purple"></i>
                                    @break
                                @case('form8')
                                    <i class="fas fa-leaf fa-2x text-success"></i>
                                    @break
                                @case('form9')
                                    <i class="fas fa-clipboard-check fa-2x text-danger"></i>
                                    @break
                                @case('form10')
                                    <i class="fas fa-calculator fa-2x text-secondary"></i>
                                    @break
                                @case('form11')
                                    <i class="fas fa-history fa-2x text-dark"></i>
                                    @break
                                @case('form12')
                                    <i class="fas fa-graduation-cap fa-2x text-info"></i>
                                    @break
                                @case('form13')
                                    <i class="fas fa-seedling fa-2x text-success"></i>
                                    @break
                                @default
                                    <i class="fas fa-file fa-2x text-secondary"></i>
                            @endswitch
                        </div>
                        <div>
                            <h5 class="card-title mb-1">{{ $info['name'] }}</h5>
                            <small class="text-muted">{{ $info['name_sw'] }}</small>
                        </div>
                    </div>
                    <p class="card-text text-muted small">
                        @switch($type)
                            @case('form1')
                                Record farmer identification and registration details.
                                @break
                            @case('form2')
                                Document new farm records and field information.
                                @break
                            @case('form3')
                                Record historical farm data and past seasons.
                                @break
                            @case('form4')
                                Questionnaire for improved/enhanced farm practices.
                                @break
                            @case('form5')
                                Track compliance and certification requirements.
                                @break
                            @case('form6')
                                Submit monthly performance and progress reports.
                                @break
                            @case('form8')
                                Document social and environmental standards compliance.
                                @break
                            @case('form9')
                                Complete internal inspection checklist for farmers.
                                @break
                            @case('form10')
                                Record production estimates and requirements.
                                @break
                            @case('form11')
                                Document complete farm history and records.
                                @break
                            @case('form12')
                                Record training attendance and activities.
                                @break
                            @case('form13')
                                Track seed distribution to farmers.
                                @break
                        @endswitch
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('farmer-forms.create', [$farmer, $type]) }}" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i>Fill Form
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if(count($accessibleForms) === 0)
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        No forms are available for your role. Please contact your administrator if you need access to specific forms.
    </div>
@endif
@endsection

@push('styles')
<style>
    .form-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .form-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .form-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 10px;
    }
</style>
@endpush
