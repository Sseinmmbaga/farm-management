@extends('layouts.base')

@section('title', 'Edit Farmer')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> Edit Farmer: {{ $farmer->first_name }} {{ $farmer->last_name }}
                        </h4>
                        <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Farmer
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('farmers.update', $farmer) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-user me-2"></i> Personal Information
                                </h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="{{ old('first_name', $farmer->first_name) }}" 
                                               required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="{{ old('last_name', $farmer->last_name) }}" 
                                               required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+255</span>
                                        <input type="tel"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               id="phone"
                                               name="phone"
                                               value="{{ old('phone', $farmer->phone) }}"
                                               placeholder="e.g., 712345678"
                                               required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Enter number without leading 0</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $farmer->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" 
                                            id="gender" 
                                            name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $farmer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $farmer->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $farmer->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date"
                                           class="form-control @error('date_of_birth') is-invalid @enderror"
                                           id="date_of_birth"
                                           name="date_of_birth"
                                           value="{{ old('date_of_birth', $farmer->date_of_birth?->format('Y-m-d')) }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="spouse_name" class="form-label">Na. ya Bw/Bi Shamba</label>
                                        <input type="text"
                                               class="form-control @error('spouse_name') is-invalid @enderror"
                                               id="spouse_name"
                                               name="spouse_name"
                                               value="{{ old('spouse_name', $farmer->spouse_name) }}"
                                               placeholder="Jina la mwenzi">
                                        @error('spouse_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="spouse_title" class="form-label">Bw/Bi Shamba</label>
                                        <input type="text"
                                               class="form-control @error('spouse_title') is-invalid @enderror"
                                               id="spouse_title"
                                               name="spouse_title"
                                               value="{{ old('spouse_title', $farmer->spouse_title) }}"
                                               placeholder="Cheo">
                                        @error('spouse_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i> Location Information
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="region_id" class="form-label">Region *</label>
                                    <select class="form-select @error('region_id') is-invalid @enderror" 
                                            id="region_id" 
                                            name="region_id" 
                                            required>
                                        <option value="">Select Region</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}" {{ old('region_id', $farmer->region_id) == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="district_id" class="form-label">District *</label>
                                    <select class="form-select @error('district_id') is-invalid @enderror" 
                                            id="district_id" 
                                            name="district_id" 
                                            required>
                                        <option value="">Select District</option>
                                        <!-- Districts will be loaded via JavaScript -->
                                    </select>
                                    @error('district_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="village_id" class="form-label">Village *</label>
                                    <select class="form-select @error('village_id') is-invalid @enderror"
                                            id="village_id"
                                            name="village_id"
                                            required>
                                        <option value="">Select Village</option>
                                        <!-- Villages will be loaded via JavaScript -->
                                    </select>
                                    @error('village_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="subvillage" class="form-label">Subvillage (Optional)</label>
                                    <input type="text"
                                           class="form-control @error('subvillage') is-invalid @enderror"
                                           id="subvillage"
                                           name="subvillage"
                                           value="{{ old('subvillage', $farmer->subvillage) }}"
                                           placeholder="Enter subvillage if applicable">
                                    @error('subvillage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address"
                                              name="address"
                                              rows="2">{{ old('address', $farmer->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <h5 class="mb-3 mt-4 border-bottom pb-2">
                                    <i class="fas fa-users me-2"></i> Assignment & Status
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="group_id" class="form-label">Farmer Group</label>
                                    <select class="form-select @error('group_id') is-invalid @enderror" 
                                            id="group_id" 
                                            name="group_id">
                                        <option value="">Select Group (Optional)</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}" {{ old('group_id', $farmer->group_id) == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('group_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="extension_officer_id" class="form-label">Extension Officer</label>
                                    <select class="form-select @error('extension_officer_id') is-invalid @enderror" 
                                            id="extension_officer_id" 
                                            name="extension_officer_id">
                                        <option value="">Assign Officer (Optional)</option>
                                        @foreach($officers as $officer)
                                            <option value="{{ $officer->id }}" {{ old('extension_officer_id', $farmer->extension_officer_id) == $officer->id ? 'selected' : '' }}>
                                                {{ $officer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('extension_officer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="active" {{ old('status', $farmer->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="pending" {{ old('status', $farmer->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="inactive" {{ old('status', $farmer->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-id-card me-2"></i> Identification & Notes
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="id_type" class="form-label">ID Type</label>
                                        <select class="form-select @error('id_type') is-invalid @enderror" 
                                                id="id_type" 
                                                name="id_type">
                                            <option value="">Select ID Type</option>
                                            <option value="ghana_card" {{ old('id_type', $farmer->id_type) == 'ghana_card' ? 'selected' : '' }}>Ghana Card</option>
                                            <option value="voter_id" {{ old('id_type', $farmer->id_type) == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                                            <option value="passport" {{ old('id_type', $farmer->id_type) == 'passport' ? 'selected' : '' }}>Passport</option>
                                            <option value="drivers_license" {{ old('id_type', $farmer->id_type) == 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                                            <option value="other" {{ old('id_type', $farmer->id_type) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('id_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="id_number" class="form-label">ID Number</label>
                                        <input type="text" 
                                               class="form-control @error('id_number') is-invalid @enderror" 
                                               id="id_number" 
                                               name="id_number" 
                                               value="{{ old('id_number', $farmer->id_number) }}">
                                        @error('id_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes"
                                              name="notes"
                                              rows="3">{{ old('notes', $farmer->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Swahili Fields Section -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i> Taarifa za Ziada (Additional Information)
                                </h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="land_size_description" class="form-label">Ukubwa wa eneo</label>
                                        <input type="text"
                                               class="form-control @error('land_size_description') is-invalid @enderror"
                                               id="land_size_description"
                                               name="land_size_description"
                                               value="{{ old('land_size_description', $farmer->land_size_description) }}"
                                               placeholder="Andika ukubwa wa eneo">
                                        @error('land_size_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="cotton_producers_count" class="form-label">Idadi ya wazalishaji wa pamba</label>
                                        <input type="text"
                                               class="form-control @error('cotton_producers_count') is-invalid @enderror"
                                               id="cotton_producers_count"
                                               name="cotton_producers_count"
                                               value="{{ old('cotton_producers_count', $farmer->cotton_producers_count) }}"
                                               placeholder="Idadi">
                                        @error('cotton_producers_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="lead_farmer" class="form-label">Mkulima Kiongozi</label>
                                        <input type="text"
                                               class="form-control @error('lead_farmer') is-invalid @enderror"
                                               id="lead_farmer"
                                               name="lead_farmer"
                                               value="{{ old('lead_farmer', $farmer->lead_farmer) }}"
                                               placeholder="Jina la mkulima kiongozi">
                                        @error('lead_farmer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="demo_farm" class="form-label">Shamba darasa</label>
                                        <input type="text"
                                               class="form-control @error('demo_farm') is-invalid @enderror"
                                               id="demo_farm"
                                               name="demo_farm"
                                               value="{{ old('demo_farm', $farmer->demo_farm) }}"
                                               placeholder="Taarifa za shamba darasa">
                                        @error('demo_farm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="owns_farming_tools" class="form-label">Anamiliki zana za Kilimo</label>
                                        <input type="text"
                                               class="form-control @error('owns_farming_tools') is-invalid @enderror"
                                               id="owns_farming_tools"
                                               name="owns_farming_tools"
                                               value="{{ old('owns_farming_tools', $farmer->owns_farming_tools) }}"
                                               placeholder="Orodha ya zana">
                                        @error('owns_farming_tools')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="last_prohibited_chemicals_use" class="form-label">Mara ya mwisho kutumia madawa yasiyoruhusiwa</label>
                                        <input type="text"
                                               class="form-control @error('last_prohibited_chemicals_use') is-invalid @enderror"
                                               id="last_prohibited_chemicals_use"
                                               name="last_prohibited_chemicals_use"
                                               value="{{ old('last_prohibited_chemicals_use', $farmer->last_prohibited_chemicals_use) }}"
                                               placeholder="Tarehe au kipindi">
                                        @error('last_prohibited_chemicals_use')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('farmers.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-1"></i> Update Farmer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Load districts based on selected region
    document.getElementById('region_id').addEventListener('change', function() {
        const regionId = this.value;
        const districtSelect = document.getElementById('district_id');
        const villageSelect = document.getElementById('village_id');
        
        // Clear existing options
        districtSelect.innerHTML = '<option value="">Select District</option>';
        villageSelect.innerHTML = '<option value="">Select Village</option>';
        
        if (!regionId) return;
        
        // Fetch districts for the selected region
        fetch(`/api/regions/${regionId}/districts`)
            .then(response => response.json())
            .then(data => {
                data.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading districts:', error));
    });
    
    // Load villages based on selected district
    document.getElementById('district_id').addEventListener('change', function() {
        const districtId = this.value;
        const villageSelect = document.getElementById('village_id');
        
        // Clear existing options
        villageSelect.innerHTML = '<option value="">Select Village</option>';
        
        if (!districtId) return;
        
        // Fetch villages for the selected district
        fetch(`/api/districts/${districtId}/villages`)
            .then(response => response.json())
            .then(data => {
                data.forEach(village => {
                    const option = document.createElement('option');
                    option.value = village.id;
                    option.textContent = village.name;
                    villageSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading villages:', error));
    });
    
    // If there's an old district_id selected, trigger district change to load villages
    // Pre-select district and village if farmer has them
    document.addEventListener('DOMContentLoaded', function() {
        const regionId = '{{ $farmer->region_id }}';
        const districtId = '{{ $farmer->district_id }}';
        const villageId = '{{ $farmer->village_id }}';
        
        if (regionId) {
            // Trigger region change to load districts
            const regionSelect = document.getElementById('region_id');
            regionSelect.value = regionId;
            regionSelect.dispatchEvent(new Event('change'));
            
            // After a delay, set district and trigger village load
            setTimeout(() => {
                if (districtId) {
                    const districtSelect = document.getElementById('district_id');
                    districtSelect.value = districtId;
                    districtSelect.dispatchEvent(new Event('change'));
                    
                    // After another delay, set village
                    setTimeout(() => {
                        if (villageId) {
                            const villageSelect = document.getElementById('village_id');
                            villageSelect.value = villageId;
                        }
                    }, 500);
                }
            }, 500);
        }
    });
    
    @if(old('district_id'))
        document.addEventListener('DOMContentLoaded', function() {
            const districtSelect = document.getElementById('district_id');
            districtSelect.dispatchEvent(new Event('change'));
        });
    @endif
</script>
@endpush
@endsection