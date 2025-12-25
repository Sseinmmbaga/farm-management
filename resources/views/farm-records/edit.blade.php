@extends('layouts.base')

@section('title', 'Edit Farm Record')

@push('styles')
<style>
    .form-container {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .form-section {
        border-left: 4px solid {{ $farmRecord->record_type === 'new' ? '#27ae60' : '#3498db' }};
        padding-left: 15px;
        margin-bottom: 30px;
    }

    .form-section h4 {
        color: {{ $farmRecord->record_type === 'new' ? '#27ae60' : '#3498db' }};
        margin-bottom: 15px;
    }

    .certification-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .certification-option {
        flex: 1;
        min-width: 120px;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .certification-option:hover {
        border-color: #27ae60;
    }

    .certification-option.selected {
        border-color: #27ae60;
        background-color: #e8f5e9;
    }

    .certification-option input {
        display: none;
    }

    .certification-option .cert-code {
        font-size: 1.5rem;
        font-weight: 700;
        display: block;
    }

    .certification-option .cert-label {
        font-size: 0.85rem;
        color: #666;
    }

    .cert-c0 { color: #9e9e9e; }
    .cert-c1 { color: #ff9800; }
    .cert-c2 { color: #2196f3; }
    .cert-o { color: #4caf50; }

    .land-change-section {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-radius: 10px;
        padding: 20px;
        margin-top: 15px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            @if($farmRecord->record_type === 'new')
                <i class="fas fa-edit text-success"></i> Edit New Farm Record
            @else
                <i class="fas fa-edit text-primary"></i> Edit Existing Farm Record
            @endif
        </h2>
        <a href="{{ route('farm-records.show', $farmRecord) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Record
        </a>
    </div>

    <div class="form-container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('farm-records.update', $farmRecord) }}" id="farmRecordForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="record_type" value="{{ $farmRecord->record_type }}">

            <!-- Farm & Season Selection -->
            <div class="form-section">
                <h4><i class="fas fa-tractor"></i> Farm & Season</h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="farm_id" class="form-label">Farm *</label>
                        <select class="form-select" id="farm_id" name="farm_id" required>
                            <option value="">-- Select Farm --</option>
                            @foreach($farms as $farmOption)
                                <option value="{{ $farmOption->id }}"
                                        {{ old('farm_id', $farmRecord->farm_id) == $farmOption->id ? 'selected' : '' }}>
                                    {{ $farmOption->code }} - {{ $farmOption->display_name }}
                                    ({{ $farmOption->farmer->full_name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="season_id" class="form-label">Season *</label>
                        <select class="form-select" id="season_id" name="season_id" required>
                            <option value="">-- Select Season --</option>
                            @foreach($seasons as $seasonOption)
                                <option value="{{ $seasonOption->id }}"
                                        {{ old('season_id', $farmRecord->season_id) == $seasonOption->id ? 'selected' : '' }}>
                                    {{ $seasonOption->name }}
                                    @if($seasonOption->is_current) (Current) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Certification Status -->
            <div class="form-section">
                <h4><i class="fas fa-certificate"></i> Certification Status</h4>

                <div class="certification-options">
                    <label class="certification-option {{ old('certification_status', $farmRecord->certification_status) == 'C0' ? 'selected' : '' }}">
                        <input type="radio" name="certification_status" value="C0"
                               {{ old('certification_status', $farmRecord->certification_status) == 'C0' ? 'checked' : '' }}>
                        <span class="cert-code cert-c0">C0</span>
                        <span class="cert-label">Conventional</span>
                    </label>

                    <label class="certification-option {{ old('certification_status', $farmRecord->certification_status) == 'C1' ? 'selected' : '' }}">
                        <input type="radio" name="certification_status" value="C1"
                               {{ old('certification_status', $farmRecord->certification_status) == 'C1' ? 'checked' : '' }}>
                        <span class="cert-code cert-c1">C1</span>
                        <span class="cert-label">Year 1 Conversion</span>
                    </label>

                    <label class="certification-option {{ old('certification_status', $farmRecord->certification_status) == 'C2' ? 'selected' : '' }}">
                        <input type="radio" name="certification_status" value="C2"
                               {{ old('certification_status', $farmRecord->certification_status) == 'C2' ? 'checked' : '' }}>
                        <span class="cert-code cert-c2">C2</span>
                        <span class="cert-label">Year 2 Conversion</span>
                    </label>

                    <label class="certification-option {{ old('certification_status', $farmRecord->certification_status) == 'O' ? 'selected' : '' }}">
                        <input type="radio" name="certification_status" value="O"
                               {{ old('certification_status', $farmRecord->certification_status) == 'O' ? 'checked' : '' }}>
                        <span class="cert-code cert-o">O</span>
                        <span class="cert-label">Organic</span>
                    </label>
                </div>
            </div>

            <!-- Livestock -->
            <div class="form-section">
                <h4><i class="fas fa-paw"></i> Livestock (Mifugo)</h4>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="cattle_count" class="form-label">Cattle (Ng'ombe)</label>
                        <input type="number" class="form-control" id="cattle_count" name="cattle_count"
                               value="{{ old('cattle_count', $farmRecord->cattle_count) }}" min="0" placeholder="0">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="goats_sheep_count" class="form-label">Goats/Sheep (Mbuzi/Kondoo)</label>
                        <input type="number" class="form-control" id="goats_sheep_count" name="goats_sheep_count"
                               value="{{ old('goats_sheep_count', $farmRecord->goats_sheep_count) }}" min="0" placeholder="0">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="oxen_count" class="form-label">Oxen (Maksai)</label>
                        <input type="number" class="form-control" id="oxen_count" name="oxen_count"
                               value="{{ old('oxen_count', $farmRecord->oxen_count) }}" min="0" placeholder="0">
                    </div>
                </div>
            </div>

            <!-- Equipment -->
            <div class="form-section">
                <h4><i class="fas fa-tools"></i> Equipment (Vifaa)</h4>

                <div class="checkbox-group">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="has_input_book" name="has_input_book" value="1"
                               {{ old('has_input_book', $farmRecord->has_input_book) ? 'checked' : '' }}>
                        <label class="form-check-label" for="has_input_book">
                            <i class="fas fa-book"></i> Has Input Book (Kitabu cha Pembejeo)
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="has_pump" name="has_pump" value="1"
                               {{ old('has_pump', $farmRecord->has_pump) ? 'checked' : '' }}>
                        <label class="form-check-label" for="has_pump">
                            <i class="fas fa-spray-can"></i> Has Pump (Ana Pampu)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Chemical/Residue Status -->
            <div class="form-section">
                <h4><i class="fas fa-flask"></i> Chemical & Residue Status</h4>

                <div class="checkbox-group">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="has_chemical_seed_residue"
                               name="has_chemical_seed_residue" value="1"
                               {{ old('has_chemical_seed_residue', $farmRecord->has_chemical_seed_residue) ? 'checked' : '' }}>
                        <label class="form-check-label" for="has_chemical_seed_residue">
                            <i class="fas fa-seedling text-warning"></i> Has Chemical Seed Residue
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="has_chemical_residue"
                               name="has_chemical_residue" value="1"
                               {{ old('has_chemical_residue', $farmRecord->has_chemical_residue) ? 'checked' : '' }}>
                        <label class="form-check-label" for="has_chemical_residue">
                            <i class="fas fa-exclamation-triangle text-danger"></i> Has Chemical Residue
                        </label>
                    </div>
                </div>
            </div>

            <!-- Farm Area -->
            <div class="form-section">
                <h4><i class="fas fa-ruler-combined"></i> Farm Area</h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="area_size" class="form-label">Area Size (Hectares)</label>
                        <input type="number" step="0.01" class="form-control" id="area_size" name="area_size"
                               value="{{ old('area_size', $farmRecord->area_size) }}" min="0" placeholder="e.g., 2.5">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="registration_year" class="form-label">Registration Year</label>
                        <input type="number" class="form-control" id="registration_year" name="registration_year"
                               value="{{ old('registration_year', $farmRecord->registration_year) }}"
                               min="1900" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                    </div>
                </div>
            </div>

            @if($farmRecord->record_type === 'existing')
            <!-- Land Changes -->
            <div class="form-section">
                <h4><i class="fas fa-exchange-alt"></i> Land Changes (Mabadiliko ya Ardhi)</h4>

                <div class="land-change-section">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="land_bought" class="form-label text-success">
                                <i class="fas fa-plus-circle"></i> Land Bought (ha)
                            </label>
                            <input type="number" step="0.01" class="form-control" id="land_bought" name="land_bought"
                                   value="{{ old('land_bought', $farmRecord->land_bought) }}" min="0" placeholder="0.00">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="land_sold" class="form-label text-danger">
                                <i class="fas fa-minus-circle"></i> Land Sold (ha)
                            </label>
                            <input type="number" step="0.01" class="form-control" id="land_sold" name="land_sold"
                                   value="{{ old('land_sold', $farmRecord->land_sold) }}" min="0" placeholder="0.00">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="land_borrowed" class="form-label text-info">
                                <i class="fas fa-hand-holding"></i> Land Borrowed (ha)
                            </label>
                            <input type="number" step="0.01" class="form-control" id="land_borrowed" name="land_borrowed"
                                   value="{{ old('land_borrowed', $farmRecord->land_borrowed) }}" min="0" placeholder="0.00">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="land_lent" class="form-label text-warning">
                                <i class="fas fa-hand-paper"></i> Land Lent (ha)
                            </label>
                            <input type="number" step="0.01" class="form-control" id="land_lent" name="land_lent"
                                   value="{{ old('land_lent', $farmRecord->land_lent) }}" min="0" placeholder="0.00">
                        </div>
                    </div>

                    <div class="alert alert-info mb-0" id="netLandChange">
                        <strong>Net Land Change:</strong> <span id="netChangeValue">0.00</span> ha
                    </div>
                </div>
            </div>
            @endif

            <!-- Impact & Notes -->
            <div class="form-section">
                <h4><i class="fas fa-sticky-note"></i> Additional Information</h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="impact_type" class="form-label">Impact Type</label>
                        <select class="form-select" id="impact_type" name="impact_type">
                            <option value="">-- Select Impact Type --</option>
                            <option value="positive" {{ old('impact_type', $farmRecord->impact_type) == 'positive' ? 'selected' : '' }}>Positive</option>
                            <option value="neutral" {{ old('impact_type', $farmRecord->impact_type) == 'neutral' ? 'selected' : '' }}>Neutral</option>
                            <option value="negative" {{ old('impact_type', $farmRecord->impact_type) == 'negative' ? 'selected' : '' }}>Negative</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (Maelezo)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                              placeholder="Any additional notes about this record...">{{ old('notes', $farmRecord->notes) }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('farm-records.show', $farmRecord) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-{{ $farmRecord->record_type === 'new' ? 'success' : 'primary' }}">
                    <i class="fas fa-save"></i> Update Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Certification option selection
        document.querySelectorAll('.certification-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.certification-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        @if($farmRecord->record_type === 'existing')
        // Calculate net land change
        function calculateNetChange() {
            const bought = parseFloat(document.getElementById('land_bought').value) || 0;
            const sold = parseFloat(document.getElementById('land_sold').value) || 0;
            const borrowed = parseFloat(document.getElementById('land_borrowed').value) || 0;
            const lent = parseFloat(document.getElementById('land_lent').value) || 0;

            const net = (bought + borrowed) - (sold + lent);
            const netSpan = document.getElementById('netChangeValue');
            netSpan.textContent = net.toFixed(2);

            const alertDiv = document.getElementById('netLandChange');
            if (net > 0) {
                alertDiv.className = 'alert alert-success mb-0';
                netSpan.textContent = '+' + net.toFixed(2);
            } else if (net < 0) {
                alertDiv.className = 'alert alert-danger mb-0';
            } else {
                alertDiv.className = 'alert alert-info mb-0';
            }
        }

        ['land_bought', 'land_sold', 'land_borrowed', 'land_lent'].forEach(id => {
            document.getElementById(id).addEventListener('input', calculateNetChange);
        });

        calculateNetChange();
        @endif
    });
</script>
@endpush
