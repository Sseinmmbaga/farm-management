@extends('layouts.base')

@section('title', 'Training Coordinator Dashboard')

@push('styles')
<style>
    .stats-card {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }
    .stats-card:hover { transform: translateY(-5px); }
    .stats-icon { font-size: 2.5rem; margin-bottom: 15px; }
    .stats-number { font-size: 2rem; font-weight: bold; margin-bottom: 5px; }
    .stats-label { color: #6c757d; font-size: 0.9rem; }
    .card-primary { border-left: 4px solid #3498db; }
    .card-success { border-left: 4px solid #2ecc71; }
    .card-warning { border-left: 4px solid #f39c12; }
    .card-teal { border-left: 4px solid #1abc9c; }
</style>
@endpush

@section('content')
    <div class="header">
        <h1 class="h3 mb-0">Training Coordinator Dashboard</h1>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-info" id="refreshBtn">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <p class="lead mb-4">Welcome! Manage training programs and certifications here.</p>

    <div class="row">
        <div class="col-md-3">
            <div class="stats-card card-primary">
                <div class="stats-icon text-primary"><i class="fas fa-calendar-check"></i></div>
                <div class="stats-number">{{ $totalPrograms ?? 0 }}</div>
                <div class="stats-label">Training Programs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-warning">
                <div class="stats-icon text-warning"><i class="fas fa-clock"></i></div>
                <div class="stats-number">{{ $upcomingSessions ?? 0 }}</div>
                <div class="stats-label">Upcoming Sessions</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-success">
                <div class="stats-icon text-success"><i class="fas fa-user-graduate"></i></div>
                <div class="stats-number">{{ $trainedFarmers ?? 0 }}</div>
                <div class="stats-label">Trained Farmers</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card card-teal">
                <div class="stats-icon" style="color: #1abc9c;"><i class="fas fa-certificate"></i></div>
                <div class="stats-number">{{ $certificatesIssued ?? 0 }}</div>
                <div class="stats-label">Certificates Issued</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Training Programs</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('training-programs.index') }}" class="btn btn-outline-info w-100 mb-2"><i class="fas fa-list"></i> All Programs</a>
                    <a href="{{ route('training-programs.create') }}" class="btn btn-outline-success w-100"><i class="fas fa-plus"></i> Create Program</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Attendance & Certification</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('training.attendance.list') }}" class="btn btn-outline-success w-100 mb-2"><i class="fas fa-user-check"></i> Record Attendance</a>
                    <a href="{{ route('training.certificates.index') }}" class="btn btn-outline-warning w-100"><i class="fas fa-certificate"></i> Issue Certificates</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3"><a href="{{ route('farmers.index') }}" class="btn btn-outline-primary w-100"><i class="fas fa-users"></i> View Farmers</a></div>
                        <div class="col-md-3"><a href="{{ route('training.sessions.index') }}" class="btn btn-outline-info w-100"><i class="fas fa-calendar-alt"></i> Schedule</a></div>
                        <div class="col-md-3"><a href="{{ route('training.reports.summary') }}" class="btn btn-outline-success w-100"><i class="fas fa-chart-bar"></i> Training Reports</a></div>
                        <div class="col-md-3"><a href="{{ route('training.reports.summary') }}" class="btn btn-outline-warning w-100"><i class="fas fa-download"></i> Export Reports</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('refreshBtn')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        setTimeout(() => location.reload(), 1000);
    });
</script>
@endpush
