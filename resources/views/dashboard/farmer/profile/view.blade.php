@extends('layouts.base')

@section('title', 'My Profile')

@push('styles')
<style>
    .profile-header {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        margin: 0 auto 20px;
    }
    .profile-details {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .detail-row {
        display: flex;
        border-bottom: 1px solid #eee;
        padding: 15px 0;
    }
    .detail-label {
        font-weight: 600;
        color: #555;
        width: 200px;
    }
    .detail-value {
        flex: 1;
        color: #333;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div>
            <h1 class="h3 mb-0">My Profile</h1>
            <p class="text-muted mb-0">View your personal and account information</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('dashboard.farmer') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @if($farmer)
            <a href="{{ route('dashboard.farmer.profile.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
            @endif
        </div>
    </div>

    @if($farmer)
    <!-- Profile Header -->
    <div class="profile-header text-center">
        <div class="profile-avatar">
            @if($farmer->photo)
            <img src="{{ Storage::url($farmer->photo) }}" alt="Profile Photo" class="w-100 h-100 rounded-circle object-fit-cover">
            @else
            <i class="fas fa-user"></i>
            @endif
        </div>
        <h2 class="mb-2">{{ $farmer->full_name ?? ($farmer->first_name . ' ' . $farmer->last_name) ?? 'Farmer' }}</h2>
        <p class="text-muted mb-0">{{ $farmer->registration_number ?? 'Registered Farmer' }}</p>
        <div class="mt-3">
            <span class="badge bg-{{ $farmer->status === 'active' ? 'success' : ($farmer->status === 'pending' ? 'warning' : 'secondary') }}">
                {{ ucfirst($farmer->status ?? 'Active') }}
            </span>
            <span class="badge bg-{{ $farmer->certification_status === 'organic' ? 'success' : ($farmer->certification_status === 'in-conversion' ? 'warning' : 'secondary') }} ms-2">
                {{ ucfirst(str_replace('-', ' ', $farmer->certification_status ?? 'Conventional')) }}
            </span>
            <span class="badge bg-info ms-2">Member since {{ $farmer->created_at?->format('M Y') ?? 'N/A' }}</span>
        </div>
    </div>

    <!-- Details -->
    <div class="profile-details">
        <h5 class="mb-4">Personal Information</h5>
        <div class="detail-row">
            <div class="detail-label">Full Name</div>
            <div class="detail-value">{{ $farmer->full_name ?? ($farmer->first_name . ' ' . ($farmer->middle_name ? $farmer->middle_name . ' ' : '') . $farmer->last_name) ?? 'Not provided' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Phone Number</div>
            <div class="detail-value">{{ $farmer->phone ?? 'Not provided' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Email Address</div>
            <div class="detail-value">{{ $farmer->email ?? 'Not provided' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">National ID</div>
            <div class="detail-value">{{ $farmer->national_id ?? 'Not provided' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Gender</div>
            <div class="detail-value">{{ ucfirst($farmer->gender ?? 'Not specified') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Region</div>
            <div class="detail-value">{{ $farmer->region?->name ?? 'Not assigned' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">District</div>
            <div class="detail-value">{{ $farmer->district?->name ?? 'Not assigned' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Village</div>
            <div class="detail-value">{{ $farmer->village?->name ?? 'Not assigned' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Farmer Group</div>
            <div class="detail-value">{{ $farmer->group?->name ?? 'Not a member' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Extension Officer</div>
            <div class="detail-value">{{ $farmer->extensionOfficer?->name ?? 'Not assigned' }}</div>
        </div>
    </div>

    <!-- Additional Sections -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Account Summary</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-tractor text-success me-2"></i> <strong>{{ $farmer->farms()->count() }}</strong> Registered Farms</li>
                        <li class="mb-2"><i class="fas fa-seedling text-info me-2"></i> <strong>{{ number_format($farmer->farms()->sum('cultivated_area'), 2) }}</strong> Hectares Cultivated</li>
                        <li class="mb-2"><i class="fas fa-chalkboard-teacher text-warning me-2"></i> <strong>{{ $farmer->trainingAttendances()->count() }}</strong> Trainings Attended</li>
                        <li class="mb-0"><i class="fas fa-box text-primary me-2"></i> <strong>{{ $farmer->stockDistributions()->count() }}</strong> Distributions Received</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Farming Focus</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><i class="fas fa-seedling text-success me-2"></i> Cotton (Pamba)</p>
                    <p class="mb-0"><i class="fas fa-leaf text-warning me-2"></i> Sesame (Ufuta)</p>
                    <hr>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Focus on organic cotton and sesame production for sustainable farming.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- No Farmer Profile -->
    <div class="text-center py-5">
        <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">Profile Not Found</h5>
        <p class="text-muted">Your farmer profile has not been set up yet.</p>
        <p class="text-muted small">Please contact your extension officer or administrator to complete your registration.</p>
        <a href="{{ route('dashboard.farmer') }}" class="btn btn-primary mt-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
    @endif
@endsection