@extends('layouts.base')

@section('title', 'Farm Record Details')

@push('styles')
<style>
    .detail-card {
        background-color: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .detail-section {
        border-left: 4px solid {{ $farmRecord->record_type === 'new' ? '#27ae60' : '#3498db' }};
        padding-left: 15px;
        margin-bottom: 25px;
    }

    .detail-section h5 {
        color: {{ $farmRecord->record_type === 'new' ? '#27ae60' : '#3498db' }};
        margin-bottom: 15px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #e0e0e0;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #666;
        font-weight: 500;
    }

    .detail-value {
        font-weight: 600;
        color: #333;
    }

    .stat-card {
        text-align: center;
        padding: 20px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    }

    .stat-card.livestock {
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    }

    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2e7d32;
    }

    .stat-card.livestock .stat-number {
        color: #e65100;
    }

    .stat-card .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    .certification-badge {
        font-size: 2rem;
        font-weight: 700;
        padding: 15px 25px;
        border-radius: 10px;
        display: inline-block;
    }

    .certification-C0 { background-color: #f5f5f5; color: #757575; }
    .certification-C1 { background-color: #fff3e0; color: #ff9800; }
    .certification-C2 { background-color: #e3f2fd; color: #2196f3; }
    .certification-O { background-color: #e8f5e9; color: #4caf50; }

    .equipment-badge {
        padding: 8px 15px;
        border-radius: 20px;
        margin-right: 10px;
        margin-bottom: 10px;
        display: inline-block;
    }

    .equipment-yes { background-color: #e8f5e9; color: #2e7d32; }
    .equipment-no { background-color: #ffebee; color: #c62828; }

    .land-change-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-radius: 10px;
        padding: 20px;
    }

    .land-change-item {
        text-align: center;
        padding: 10px;
    }

    .land-change-value {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .land-change-label {
        font-size: 0.85rem;
        color: #666;
    }

    .positive { color: #4caf50; }
    .negative { color: #f44336; }
    .neutral { color: #2196f3; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            @if($farmRecord->record_type === 'new')
                <i class="fas fa-file-alt text-success"></i> New Farm Record
            @else
                <i class="fas fa-file-alt text-primary"></i> Existing Farm Record
            @endif
        </h2>
        <div class="btn-group">
            <a href="{{ route('farm-records.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @if(!auth()->user()->hasViewOnlyAccess())
            <a href="{{ route('farm-records.edit', $farmRecord) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('farm-records.destroy', $farmRecord) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Are you sure you want to delete this record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-md-8">
            <div class="detail-card">
                <!-- Farm & Season -->
                <div class="detail-section">
                    <h5><i class="fas fa-tractor"></i> Farm & Season Information</h5>
                    <div class="detail-item">
                        <span class="detail-label">Farm</span>
                        <span class="detail-value">
                            <a href="{{ route('farms.show', $farmRecord->farm) }}">
                                {{ $farmRecord->farm->display_name ?? 'N/A' }} ({{ $farmRecord->farm->code ?? '' }})
                            </a>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Farmer</span>
                        <span class="detail-value">
                            <a href="{{ route('farmers.show', $farmRecord->farmer) }}">
                                {{ $farmRecord->farmer->full_name ?? 'N/A' }}
                            </a>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Season</span>
                        <span class="detail-value">{{ $farmRecord->season->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Record Type</span>
                        <span class="detail-value">
                            <span class="badge bg-{{ $farmRecord->record_type === 'new' ? 'success' : 'primary' }}">
                                {{ $farmRecord->record_type_label }}
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Area Size</span>
                        <span class="detail-value">{{ $farmRecord->area_size ?? 'N/A' }} ha</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Registration Year</span>
                        <span class="detail-value">{{ $farmRecord->registration_year ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Equipment & Chemical Status -->
                <div class="detail-section">
                    <h5><i class="fas fa-tools"></i> Equipment & Chemical Status</h5>

                    <div class="mb-3">
                        <strong>Equipment:</strong><br>
                        <span class="equipment-badge {{ $farmRecord->has_input_book ? 'equipment-yes' : 'equipment-no' }}">
                            <i class="fas {{ $farmRecord->has_input_book ? 'fa-check' : 'fa-times' }}"></i>
                            Input Book
                        </span>
                        <span class="equipment-badge {{ $farmRecord->has_pump ? 'equipment-yes' : 'equipment-no' }}">
                            <i class="fas {{ $farmRecord->has_pump ? 'fa-check' : 'fa-times' }}"></i>
                            Pump
                        </span>
                    </div>

                    <div>
                        <strong>Chemical Status:</strong><br>
                        <span class="equipment-badge {{ $farmRecord->has_chemical_seed_residue ? 'equipment-no' : 'equipment-yes' }}">
                            <i class="fas {{ $farmRecord->has_chemical_seed_residue ? 'fa-exclamation-triangle' : 'fa-check' }}"></i>
                            Chemical Seed Residue: {{ $farmRecord->has_chemical_seed_residue ? 'Yes' : 'No' }}
                        </span>
                        <span class="equipment-badge {{ $farmRecord->has_chemical_residue ? 'equipment-no' : 'equipment-yes' }}">
                            <i class="fas {{ $farmRecord->has_chemical_residue ? 'fa-exclamation-triangle' : 'fa-check' }}"></i>
                            Chemical Residue: {{ $farmRecord->has_chemical_residue ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>

                @if($farmRecord->record_type === 'existing')
                <!-- Land Changes -->
                <div class="detail-section">
                    <h5><i class="fas fa-exchange-alt"></i> Land Changes</h5>

                    <div class="land-change-card">
                        <div class="row">
                            <div class="col-3 land-change-item">
                                <div class="land-change-value positive">+{{ $farmRecord->land_bought ?? 0 }}</div>
                                <div class="land-change-label">Bought (ha)</div>
                            </div>
                            <div class="col-3 land-change-item">
                                <div class="land-change-value negative">-{{ $farmRecord->land_sold ?? 0 }}</div>
                                <div class="land-change-label">Sold (ha)</div>
                            </div>
                            <div class="col-3 land-change-item">
                                <div class="land-change-value neutral">+{{ $farmRecord->land_borrowed ?? 0 }}</div>
                                <div class="land-change-label">Borrowed (ha)</div>
                            </div>
                            <div class="col-3 land-change-item">
                                <div class="land-change-value negative">-{{ $farmRecord->land_lent ?? 0 }}</div>
                                <div class="land-change-label">Lent (ha)</div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <strong>Net Change:</strong>
                            <span class="{{ $farmRecord->net_land_change >= 0 ? 'positive' : 'negative' }}"
                                  style="font-size: 1.2rem; font-weight: 700;">
                                {{ $farmRecord->net_land_change >= 0 ? '+' : '' }}{{ number_format($farmRecord->net_land_change, 2) }} ha
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($farmRecord->notes)
                <div class="detail-section">
                    <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                    <p>{{ $farmRecord->notes }}</p>
                </div>
                @endif

                <!-- Audit Info -->
                <div class="detail-section">
                    <h5><i class="fas fa-history"></i> Record Information</h5>
                    <div class="detail-item">
                        <span class="detail-label">Created By</span>
                        <span class="detail-value">{{ $farmRecord->creator->name ?? 'System' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Created At</span>
                        <span class="detail-value">{{ $farmRecord->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    @if($farmRecord->updated_at != $farmRecord->created_at)
                    <div class="detail-item">
                        <span class="detail-label">Last Updated By</span>
                        <span class="detail-value">{{ $farmRecord->updater->name ?? 'System' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Last Updated At</span>
                        <span class="detail-value">{{ $farmRecord->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Certification Status -->
            <div class="detail-card text-center">
                <h5 class="mb-3">Certification Status</h5>
                <div class="certification-badge certification-{{ $farmRecord->certification_status }}">
                    {{ $farmRecord->certification_status }}
                </div>
                <p class="mt-2 mb-0 text-muted">{{ $farmRecord->certification_status_label }}</p>
            </div>

            <!-- Livestock Stats -->
            <div class="detail-card">
                <h5 class="mb-3"><i class="fas fa-paw"></i> Livestock</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="stat-card livestock">
                            <div class="stat-number">{{ $farmRecord->cattle_count }}</div>
                            <div class="stat-label">Cattle (Ng'ombe)</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card livestock">
                            <div class="stat-number">{{ $farmRecord->goats_sheep_count }}</div>
                            <div class="stat-label">Goats/Sheep</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card livestock">
                            <div class="stat-number">{{ $farmRecord->oxen_count }}</div>
                            <div class="stat-label">Oxen</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="stat-card">
                            <div class="stat-number">{{ $farmRecord->total_livestock }}</div>
                            <div class="stat-label">Total Livestock</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Impact Type -->
            @if($farmRecord->impact_type)
            <div class="detail-card text-center">
                <h5 class="mb-3">Impact Type</h5>
                <span class="badge bg-{{ $farmRecord->impact_type === 'positive' ? 'success' : ($farmRecord->impact_type === 'negative' ? 'danger' : 'secondary') }}"
                      style="font-size: 1rem; padding: 10px 20px;">
                    {{ ucfirst($farmRecord->impact_type) }}
                </span>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
