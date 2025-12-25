@extends('layouts.base')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-cogs me-2"></i> System Settings
                        </h4>
                        <a href="{{ route('dashboard.admin') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Settings Navigation -->
                        <div class="col-md-3">
                            <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
                                <button class="nav-link active text-start" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                                    <i class="fas fa-building me-2"></i> General Settings
                                </button>
                                <button class="nav-link text-start" id="farm-tab" data-bs-toggle="pill" data-bs-target="#farm" type="button" role="tab">
                                    <i class="fas fa-tractor me-2"></i> Farm Settings
                                </button>
                                <button class="nav-link text-start" id="certification-tab" data-bs-toggle="pill" data-bs-target="#certification" type="button" role="tab">
                                    <i class="fas fa-certificate me-2"></i> Certification Settings
                                </button>
                                <button class="nav-link text-start" id="notifications-tab" data-bs-toggle="pill" data-bs-target="#notifications" type="button" role="tab">
                                    <i class="fas fa-bell me-2"></i> Notification Settings
                                </button>
                            </div>
                        </div>

                        <!-- Settings Content -->
                        <div class="col-md-9">
                            <div class="tab-content" id="settings-tabContent">
                                <!-- General Settings -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-building me-2"></i> General Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('settings.update.general') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="organization_name" class="form-label">Organization Name <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                               class="form-control"
                                                               id="organization_name"
                                                               name="organization_name"
                                                               value="{{ $settings['general_organization_name'] ?? 'Remei Farm OS' }}"
                                                               required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="organization_email" class="form-label">Organization Email</label>
                                                        <input type="email"
                                                               class="form-control"
                                                               id="organization_email"
                                                               name="organization_email"
                                                               value="{{ $settings['general_organization_email'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="organization_phone" class="form-label">Organization Phone</label>
                                                        <input type="text"
                                                               class="form-control"
                                                               id="organization_phone"
                                                               name="organization_phone"
                                                               value="{{ $settings['general_organization_phone'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="default_currency" class="form-label">Default Currency <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="default_currency" name="default_currency" required>
                                                            <option value="TZS" {{ ($settings['general_default_currency'] ?? 'TZS') === 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                                                            <option value="USD" {{ ($settings['general_default_currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                            <option value="EUR" {{ ($settings['general_default_currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                            <option value="KES" {{ ($settings['general_default_currency'] ?? '') === 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                                                            <option value="UGX" {{ ($settings['general_default_currency'] ?? '') === 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label for="organization_address" class="form-label">Organization Address</label>
                                                        <textarea class="form-control"
                                                                  id="organization_address"
                                                                  name="organization_address"
                                                                  rows="2">{{ $settings['general_organization_address'] ?? '' }}</textarea>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="date_format" class="form-label">Date Format <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="date_format" name="date_format" required>
                                                            <option value="d/m/Y" {{ ($settings['general_date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2024)</option>
                                                            <option value="m/d/Y" {{ ($settings['general_date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2024)</option>
                                                            <option value="Y-m-d" {{ ($settings['general_date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2024-12-31)</option>
                                                            <option value="d-m-Y" {{ ($settings['general_date_format'] ?? '') === 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (31-12-2024)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="timezone" name="timezone" required>
                                                            <option value="Africa/Dar_es_Salaam" {{ ($settings['general_timezone'] ?? 'Africa/Dar_es_Salaam') === 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Africa/Dar_es_Salaam (EAT)</option>
                                                            <option value="Africa/Nairobi" {{ ($settings['general_timezone'] ?? '') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                                                            <option value="Africa/Kampala" {{ ($settings['general_timezone'] ?? '') === 'Africa/Kampala' ? 'selected' : '' }}>Africa/Kampala (EAT)</option>
                                                            <option value="UTC" {{ ($settings['general_timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i> Save General Settings
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Farm Settings -->
                                <div class="tab-pane fade" id="farm" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-tractor me-2"></i> Farm Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('settings.update.farm') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="default_farm_unit" class="form-label">Default Farm Size Unit <span class="text-danger">*</span></label>
                                                        <select class="form-select" id="default_farm_unit" name="default_farm_unit" required>
                                                            <option value="hectares" {{ ($settings['farm_default_unit'] ?? 'hectares') === 'hectares' ? 'selected' : '' }}>Hectares</option>
                                                            <option value="acres" {{ ($settings['farm_default_unit'] ?? '') === 'acres' ? 'selected' : '' }}>Acres</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="default_season_duration" class="form-label">Default Season Duration (months) <span class="text-danger">*</span></label>
                                                        <input type="number"
                                                               class="form-control"
                                                               id="default_season_duration"
                                                               name="default_season_duration"
                                                               min="1"
                                                               max="24"
                                                               value="{{ $settings['farm_season_duration'] ?? 6 }}"
                                                               required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="min_farm_size" class="form-label">Minimum Farm Size <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="number"
                                                                   class="form-control"
                                                                   id="min_farm_size"
                                                                   name="min_farm_size"
                                                                   step="0.01"
                                                                   min="0"
                                                                   value="{{ $settings['farm_min_size'] ?? 0.1 }}"
                                                                   required>
                                                            <span class="input-group-text">ha</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="max_farm_size" class="form-label">Maximum Farm Size <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="number"
                                                                   class="form-control"
                                                                   id="max_farm_size"
                                                                   name="max_farm_size"
                                                                   step="0.01"
                                                                   min="0"
                                                                   value="{{ $settings['farm_max_size'] ?? 100 }}"
                                                                   required>
                                                            <span class="input-group-text">ha</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   id="require_gps_coordinates"
                                                                   name="require_gps_coordinates"
                                                                   value="1"
                                                                   {{ ($settings['farm_require_gps'] ?? '1') === '1' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="require_gps_coordinates">
                                                                Require GPS Coordinates for Farms
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   id="require_farm_boundaries"
                                                                   name="require_farm_boundaries"
                                                                   value="1"
                                                                   {{ ($settings['farm_require_boundaries'] ?? '0') === '1' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="require_farm_boundaries">
                                                                Require Farm Boundaries (Polygon)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i> Save Farm Settings
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Certification Settings -->
                                <div class="tab-pane fade" id="certification" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-certificate me-2"></i> Certification Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('settings.update.certification') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="conversion_period_months" class="form-label">Conversion Period (months) <span class="text-danger">*</span></label>
                                                        <input type="number"
                                                               class="form-control"
                                                               id="conversion_period_months"
                                                               name="conversion_period_months"
                                                               min="12"
                                                               max="60"
                                                               value="{{ $settings['certification_conversion_period'] ?? 36 }}"
                                                               required>
                                                        <div class="form-text">Standard organic conversion period is 36 months (3 years)</div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="inspection_frequency_months" class="form-label">Inspection Frequency (months) <span class="text-danger">*</span></label>
                                                        <input type="number"
                                                               class="form-control"
                                                               id="inspection_frequency_months"
                                                               name="inspection_frequency_months"
                                                               min="1"
                                                               max="24"
                                                               value="{{ $settings['certification_inspection_frequency'] ?? 12 }}"
                                                               required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="certification_validity_months" class="form-label">Certification Validity (months) <span class="text-danger">*</span></label>
                                                        <input type="number"
                                                               class="form-control"
                                                               id="certification_validity_months"
                                                               name="certification_validity_months"
                                                               min="6"
                                                               max="60"
                                                               value="{{ $settings['certification_validity'] ?? 12 }}"
                                                               required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check form-switch mt-4">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   id="require_annual_inspection"
                                                                   name="require_annual_inspection"
                                                                   value="1"
                                                                   {{ ($settings['certification_require_annual'] ?? '1') === '1' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="require_annual_inspection">
                                                                Require Annual Inspection
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   id="auto_expire_certification"
                                                                   name="auto_expire_certification"
                                                                   value="1"
                                                                   {{ ($settings['certification_auto_expire'] ?? '1') === '1' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="auto_expire_certification">
                                                                Auto-expire Certification
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i> Save Certification Settings
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notification Settings -->
                                <div class="tab-pane fade" id="notifications" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="fas fa-bell me-2"></i> Notification Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('settings.update.notifications') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   id="email_notifications"
                                                                   name="email_notifications"
                                                                   value="1"
                                                                   {{ ($settings['notification_email_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="email_notifications">
                                                                Enable Email Notifications
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   id="sms_notifications"
                                                                   name="sms_notifications"
                                                                   value="1"
                                                                   {{ ($settings['notification_sms_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="sms_notifications">
                                                                Enable SMS Notifications
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   id="task_reminder_enabled"
                                                                   name="task_reminder_enabled"
                                                                   value="1"
                                                                   {{ ($settings['notification_task_reminder'] ?? '1') === '1' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="task_reminder_enabled">
                                                                Enable Task Reminders
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="inspection_reminder_days" class="form-label">Inspection Reminder (days before) <span class="text-danger">*</span></label>
                                                        <input type="number"
                                                               class="form-control"
                                                               id="inspection_reminder_days"
                                                               name="inspection_reminder_days"
                                                               min="1"
                                                               max="30"
                                                               value="{{ $settings['notification_inspection_reminder'] ?? 7 }}"
                                                               required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="certification_expiry_reminder_days" class="form-label">Certification Expiry Reminder (days before) <span class="text-danger">*</span></label>
                                                        <input type="number"
                                                               class="form-control"
                                                               id="certification_expiry_reminder_days"
                                                               name="certification_expiry_reminder_days"
                                                               min="1"
                                                               max="90"
                                                               value="{{ $settings['notification_certification_reminder'] ?? 30 }}"
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i> Save Notification Settings
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .nav-pills .nav-link {
        border-radius: 0;
        border-left: 3px solid transparent;
        color: #333;
    }
    .nav-pills .nav-link:hover {
        background-color: #f8f9fa;
        border-left-color: #6c757d;
    }
    .nav-pills .nav-link.active {
        background-color: #e9ecef;
        color: #0d6efd;
        border-left-color: #0d6efd;
    }
</style>
@endpush
@endsection
