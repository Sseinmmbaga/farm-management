@extends('layouts.base')

@section('title', isset($farmerForm) ? 'Edit ' . $formInfo['name'] : $formInfo['name'])

@section('content')
<div class="header">
    <div>
        <h4 class="mb-0">{{ $formInfo['name'] }}</h4>
        <small class="text-muted">{{ $formInfo['name_sw'] ?? 'Fomu ya Usambazaji wa Mbegu' }} - Seed Distribution Form</small>
    </div>
    <a href="{{ route('farmer-forms.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ isset($farmerForm) ? route('farmer-forms.update', $farmerForm) : route('farmer-forms.store', $formType) }}" method="POST">
    @csrf
    @if(isset($farmerForm))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-seedling me-2"></i>Seed Distribution Form</h5>
                </div>
                <div class="card-body">
                    <!-- Distribution Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Season / Msimu <span class="text-danger">*</span></label>
                            <input type="text" name="season" class="form-control" value="{{ old('season', $farmerForm->season ?? '') }}" placeholder="e.g., 2024/2025" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Distribution Date / Tarehe ya Usambazaji</label>
                            <input type="date" name="form_date" class="form-control" value="{{ old('form_date', isset($farmerForm) ? $farmerForm->form_date?->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Distribution Reference / Rejea ya Usambazaji</label>
                            <input type="text" name="form_data[distribution_reference]" class="form-control" value="{{ old('form_data.distribution_reference', $farmerForm->form_data['distribution_reference'] ?? 'SD-' . date('Ymd') . '-' . rand(100, 999)) }}">
                        </div>
                    </div>

                    <!-- Distribution Location -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Distribution Location / Mahali pa Usambazaji</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Distribution Point / Sehemu ya Usambazaji</label>
                                <input type="text" name="form_data[distribution_point]" class="form-control" value="{{ old('form_data.distribution_point', $farmerForm->form_data['distribution_point'] ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catchment Area / Eneo la Uvunaji</label>
                                <input type="text" name="form_data[catchment_area]" class="form-control" value="{{ old('form_data.catchment_area', $farmerForm->form_data['catchment_area'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Distributed By / Amesambazwa na</label>
                                <input type="text" name="form_data[distributed_by]" class="form-control" value="{{ old('form_data.distributed_by', $farmerForm->form_data['distributed_by'] ?? auth()->user()->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Supervisor / Msimamizi</label>
                                <input type="text" name="form_data[supervisor_name]" class="form-control" value="{{ old('form_data.supervisor_name', $farmerForm->form_data['supervisor_name'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Seed Details -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-leaf me-2"></i>Seed Details / Maelezo ya Mbegu</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Seed Type / Aina ya Mbegu <span class="text-danger">*</span></label>
                                <select name="form_data[seed_type]" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="cotton_organic" {{ old('form_data.seed_type', $farmerForm->form_data['seed_type'] ?? '') == 'cotton_organic' ? 'selected' : '' }}>Organic Cotton / Pamba ya Kilimo Hai</option>
                                    <option value="cotton_conventional" {{ old('form_data.seed_type', $farmerForm->form_data['seed_type'] ?? '') == 'cotton_conventional' ? 'selected' : '' }}>Conventional Cotton / Pamba ya Kawaida</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Seed Variety / Aina ya Mbegu</label>
                                <input type="text" name="form_data[seed_variety]" class="form-control" value="{{ old('form_data.seed_variety', $farmerForm->form_data['seed_variety'] ?? '') }}" placeholder="e.g., UK-91">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Batch/Lot Number / Namba ya Kundi</label>
                                <input type="text" name="form_data[batch_number]" class="form-control" value="{{ old('form_data.batch_number', $farmerForm->form_data['batch_number'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Total Quantity Distributed (kg) / Jumla Iliyosambazwa</label>
                                <input type="number" step="0.1" name="form_data[total_quantity]" class="form-control" value="{{ old('form_data.total_quantity', $farmerForm->form_data['total_quantity'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Seed Certification / Uthibitisho wa Mbegu</label>
                                <select name="form_data[seed_certification]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="certified" {{ old('form_data.seed_certification', $farmerForm->form_data['seed_certification'] ?? '') == 'certified' ? 'selected' : '' }}>Certified / Zimethibitishwa</option>
                                    <option value="quality_declared" {{ old('form_data.seed_certification', $farmerForm->form_data['seed_certification'] ?? '') == 'quality_declared' ? 'selected' : '' }}>Quality Declared / Ubora Umethibitishwa</option>
                                    <option value="farmer_saved" {{ old('form_data.seed_certification', $farmerForm->form_data['seed_certification'] ?? '') == 'farmer_saved' ? 'selected' : '' }}>Farmer Saved / Zilizohifadhiwa na Mkulima</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Source / Chanzo</label>
                                <input type="text" name="form_data[seed_source]" class="form-control" value="{{ old('form_data.seed_source', $farmerForm->form_data['seed_source'] ?? '') }}" placeholder="e.g., Remei Stock">
                            </div>
                        </div>
                    </div>

                    <!-- Distribution Summary -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Distribution Summary / Muhtasari wa Usambazaji</h6>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Farmers Received / Wakulima Waliopokea</label>
                                <input type="number" name="form_data[farmers_received]" class="form-control" value="{{ old('form_data.farmers_received', $farmerForm->form_data['farmers_received'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Male Farmers / Wakulima wa Kiume</label>
                                <input type="number" name="form_data[male_farmers]" class="form-control" value="{{ old('form_data.male_farmers', $farmerForm->form_data['male_farmers'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Female Farmers / Wakulima wa Kike</label>
                                <input type="number" name="form_data[female_farmers]" class="form-control" value="{{ old('form_data.female_farmers', $farmerForm->form_data['female_farmers'] ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Youth Farmers / Vijana</label>
                                <input type="number" name="form_data[youth_farmers]" class="form-control" value="{{ old('form_data.youth_farmers', $farmerForm->form_data['youth_farmers'] ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Average per Farmer (kg) / Wastani kwa Mkulima</label>
                                <input type="number" step="0.1" name="form_data[average_per_farmer]" class="form-control" value="{{ old('form_data.average_per_farmer', $farmerForm->form_data['average_per_farmer'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Minimum Distribution (kg) / Kiwango cha Chini</label>
                                <input type="number" step="0.1" name="form_data[min_distribution]" class="form-control" value="{{ old('form_data.min_distribution', $farmerForm->form_data['min_distribution'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Maximum Distribution (kg) / Kiwango cha Juu</label>
                                <input type="number" step="0.1" name="form_data[max_distribution]" class="form-control" value="{{ old('form_data.max_distribution', $farmerForm->form_data['max_distribution'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Individual Farmer Recipient (Optional) -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-user me-2"></i>Individual Farmer Recipient (Optional) / Mkulima Mmoja (Hiari)</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Farmer / Chagua Mkulima</label>
                                <select name="farmer_id" class="form-select">
                                    <option value="">-- Select Farmer (if individual distribution) --</option>
                                    @foreach($farmers as $f)
                                        <option value="{{ $f->id }}" {{ old('farmer_id', $farmerForm->farmer_id ?? ($farmer->id ?? '')) == $f->id ? 'selected' : '' }}>
                                            {{ $f->first_name }} {{ $f->last_name }} ({{ $f->registration_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity Received (kg) / Kiasi Alichopokea</label>
                                <input type="number" step="0.1" name="form_data[individual_quantity]" class="form-control" value="{{ old('form_data.individual_quantity', $farmerForm->form_data['individual_quantity'] ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Distribution Conditions -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-clipboard-list me-2"></i>Distribution Conditions / Masharti ya Usambazaji</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Payment Type / Aina ya Malipo</label>
                                <select name="form_data[payment_type]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="free" {{ old('form_data.payment_type', $farmerForm->form_data['payment_type'] ?? '') == 'free' ? 'selected' : '' }}>Free / Bila Malipo</option>
                                    <option value="subsidized" {{ old('form_data.payment_type', $farmerForm->form_data['payment_type'] ?? '') == 'subsidized' ? 'selected' : '' }}>Subsidized / Ruzuku</option>
                                    <option value="credit" {{ old('form_data.payment_type', $farmerForm->form_data['payment_type'] ?? '') == 'credit' ? 'selected' : '' }}>Credit / Mkopo</option>
                                    <option value="cash" {{ old('form_data.payment_type', $farmerForm->form_data['payment_type'] ?? '') == 'cash' ? 'selected' : '' }}>Cash / Taslimu</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price per kg (TZS) / Bei kwa kg</label>
                                <input type="number" name="form_data[price_per_kg]" class="form-control" value="{{ old('form_data.price_per_kg', $farmerForm->form_data['price_per_kg'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total Value (TZS) / Thamani Jumla</label>
                                <input type="number" name="form_data[total_value]" class="form-control" value="{{ old('form_data.total_value', $farmerForm->form_data['total_value'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Farmers Signed Receipt / Wakulima Wamesaini Risiti</label>
                            <select name="form_data[signed_receipts]" class="form-select">
                                <option value="">Select</option>
                                <option value="yes" {{ old('form_data.signed_receipts', $farmerForm->form_data['signed_receipts'] ?? '') == 'yes' ? 'selected' : '' }}>Yes / Ndiyo</option>
                                <option value="no" {{ old('form_data.signed_receipts', $farmerForm->form_data['signed_receipts'] ?? '') == 'no' ? 'selected' : '' }}>No / Hapana</option>
                            </select>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-warehouse me-2"></i>Stock Information / Taarifa za Hisa</h6>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Opening Stock (kg) / Hisa za Mwanzo</label>
                                <input type="number" step="0.1" name="form_data[opening_stock]" class="form-control" value="{{ old('form_data.opening_stock', $farmerForm->form_data['opening_stock'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity Distributed (kg) / Kiasi Kilichosambazwa</label>
                                <input type="number" step="0.1" name="form_data[quantity_distributed]" class="form-control" value="{{ old('form_data.quantity_distributed', $farmerForm->form_data['quantity_distributed'] ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Closing Stock (kg) / Hisa za Mwisho</label>
                                <input type="number" step="0.1" name="form_data[closing_stock]" class="form-control" value="{{ old('form_data.closing_stock', $farmerForm->form_data['closing_stock'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock Verification Notes / Maelezo ya Uthibitisho wa Hisa</label>
                            <textarea name="form_data[stock_notes]" class="form-control" rows="2">{{ old('form_data.stock_notes', $farmerForm->form_data['stock_notes'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Issues & Observations -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-exclamation-circle me-2"></i>Issues & Observations / Matatizo na Uchunguzi</h6>

                        <div class="mb-3">
                            <label class="form-label">Issues Encountered / Matatizo Yaliyokutwa</label>
                            <textarea name="form_data[issues_encountered]" class="form-control" rows="2">{{ old('form_data.issues_encountered', $farmerForm->form_data['issues_encountered'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observations / Uchunguzi</label>
                            <textarea name="form_data[observations]" class="form-control" rows="2">{{ old('form_data.observations', $farmerForm->form_data['observations'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mb-3">
                        <label class="form-label">Additional Notes / Maelezo Mengine</label>
                        <textarea name="form_data[notes]" class="form-control" rows="3">{{ old('form_data.notes', $farmerForm->form_data['notes'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Form Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" name="status" value="submitted" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Submit Form
                        </button>
                        <button type="submit" name="status" value="draft" class="btn btn-outline-secondary">
                            <i class="fas fa-save me-2"></i>Save as Draft
                        </button>
                        <a href="{{ route('farmer-forms.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Form Info</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <strong>Form 13:</strong> Fomu ya Usambazaji wa Mbegu
                    </p>
                    <p class="small text-muted mb-0">
                        This form tracks seed distribution to farmers including seed details, distribution summary, payment conditions, and stock reconciliation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
