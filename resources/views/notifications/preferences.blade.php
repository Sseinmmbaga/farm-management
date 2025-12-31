@extends('layouts.admin')

@section('title', 'Notification Preferences')

@section('content')
<div class="container-fluid">
    <div class="header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Notification Preferences</h4>
                <small class="text-muted">Manage how you receive notifications</small>
            </div>
            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Notifications
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('notifications.preferences.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Notification Channels -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-broadcast-tower me-2"></i> Notification Channels</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Choose how you want to receive notifications.</p>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="in_app_enabled" name="in_app_enabled"
                                   {{ $preferences->in_app_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="in_app_enabled">
                                <strong>In-App Notifications</strong>
                                <br><small class="text-muted">Receive notifications within the application</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="email_enabled" name="email_enabled"
                                   {{ $preferences->email_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_enabled">
                                <strong>Email Notifications</strong>
                                <br><small class="text-muted">Receive notifications via email at {{ auth()->user()->email }}</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="sms_enabled" name="sms_enabled"
                                   {{ $preferences->sms_enabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_enabled">
                                <strong>SMS Notifications</strong>
                                <br><small class="text-muted">Receive notifications via SMS at {{ auth()->user()->phone ?? 'No phone number' }}</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Categories -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i> Notification Categories</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Choose which types of notifications you want to receive.</p>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="certification_alerts" name="certification_alerts"
                                   {{ $preferences->certification_alerts ? 'checked' : '' }}>
                            <label class="form-check-label" for="certification_alerts">
                                <strong><i class="fas fa-certificate text-success me-1"></i> Certification Alerts</strong>
                                <br><small class="text-muted">Expiring certifications and compliance updates</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="document_alerts" name="document_alerts"
                                   {{ $preferences->document_alerts ? 'checked' : '' }}>
                            <label class="form-check-label" for="document_alerts">
                                <strong><i class="fas fa-file-alt text-warning me-1"></i> Document Alerts</strong>
                                <br><small class="text-muted">Expiring documents and required updates</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="training_reminders" name="training_reminders"
                                   {{ $preferences->training_reminders ? 'checked' : '' }}>
                            <label class="form-check-label" for="training_reminders">
                                <strong><i class="fas fa-chalkboard-teacher text-info me-1"></i> Training Reminders</strong>
                                <br><small class="text-muted">Upcoming training sessions and certificate expiry</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="service_request_updates" name="service_request_updates"
                                   {{ $preferences->service_request_updates ? 'checked' : '' }}>
                            <label class="form-check-label" for="service_request_updates">
                                <strong><i class="fas fa-headset text-primary me-1"></i> Service Request Updates</strong>
                                <br><small class="text-muted">Updates on your service requests</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="system_announcements" name="system_announcements"
                                   {{ $preferences->system_announcements ? 'checked' : '' }}>
                            <label class="form-check-label" for="system_announcements">
                                <strong><i class="fas fa-bullhorn text-danger me-1"></i> System Announcements</strong>
                                <br><small class="text-muted">Important system updates and announcements</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reminder Timing -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Reminder Timing</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Set how many days in advance you want to receive reminders.</p>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="certification_reminder_days" class="form-label">
                                <i class="fas fa-certificate text-success me-1"></i> Certification Reminders
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="certification_reminder_days"
                                       name="certification_reminder_days" value="{{ $preferences->certification_reminder_days }}"
                                       min="1" max="90">
                                <span class="input-group-text">days before</span>
                            </div>
                            <small class="text-muted">Notify before certification expires</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="document_reminder_days" class="form-label">
                                <i class="fas fa-file-alt text-warning me-1"></i> Document Reminders
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="document_reminder_days"
                                       name="document_reminder_days" value="{{ $preferences->document_reminder_days }}"
                                       min="1" max="90">
                                <span class="input-group-text">days before</span>
                            </div>
                            <small class="text-muted">Notify before document expires</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="training_reminder_days" class="form-label">
                                <i class="fas fa-chalkboard-teacher text-info me-1"></i> Training Reminders
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="training_reminder_days"
                                       name="training_reminder_days" value="{{ $preferences->training_reminder_days }}"
                                       min="1" max="30">
                                <span class="input-group-text">days before</span>
                            </div>
                            <small class="text-muted">Notify before training session</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('notifications.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save Preferences
            </button>
        </div>
    </form>
</div>
@endsection
