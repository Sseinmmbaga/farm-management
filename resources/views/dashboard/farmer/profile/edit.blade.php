@extends('layouts.base')

@section('title', 'Edit Profile')

@push('styles')
<style>
    .profile-form-card {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .form-label {
        font-weight: 600;
        color: #555;
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #27ae60;
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">Edit Profile</h1>
            <p class="text-muted mb-0">Update your personal information</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer.profile.view') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="profile-form-card mt-4">
        <form action="#" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" value="{{ $farmer->full_name ?? $farmer->name ?? '' }}" placeholder="Enter your full name">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" value="{{ $farmer->phone ?? '' }}" placeholder="Enter phone number">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="{{ $farmer->email ?? '' }}" placeholder="Enter email address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ID Number</label>
                    <input type="text" class="form-control" value="{{ $farmer->id_number ?? '' }}" placeholder="National ID">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Region</label>
                    <select class="form-select">
                        <option>Select region</option>
                        <option selected>{{ $farmer->region->name ?? 'Not assigned' }}</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Village</label>
                    <select class="form-select">
                        <option>Select village</option>
                        <option selected>{{ $farmer->village->name ?? 'Not assigned' }}</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" rows="2" placeholder="Enter your address">{{ $farmer->address ?? '' }}</textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Profile Bio</label>
                    <textarea class="form-control" rows="3" placeholder="Tell us about yourself">{{ $farmer->bio ?? '' }}</textarea>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <a href="{{ route('dashboard.farmer.profile.view') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="card border-danger mt-5">
        <div class="card-header bg-danger text-white">
            <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h6>
        </div>
        <div class="card-body">
            <p class="text-muted">Be careful with these actions. They cannot be undone.</p>
            <div class="d-flex gap-3">
                <button type="button" class="btn btn-outline-danger">
                    <i class="fas fa-key"></i> Change Password
                </button>
                <button type="button" class="btn btn-outline-danger">
                    <i class="fas fa-ban"></i> Deactivate Account
                </button>
            </div>
        </div>
    </div>
@endsection