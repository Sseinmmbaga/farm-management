@extends('layouts.base')

@section('title', 'Training History - ' . $farmer->first_name . ' ' . $farmer->last_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-graduate me-2"></i> Training History
                        </h4>
                        <a href="{{ route('farmers.show', $farmer) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Farmer
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Farmer Info --}}
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i> Farmer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($farmer->first_name, 0, 1)) }}{{ strtoupper(substr($farmer->last_name, 0, 1)) }}
                                </div>
                            </div>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $farmer->first_name }} {{ $farmer->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Reg. No:</th>
                                    <td><code>{{ $farmer->registration_number }}</code></td>
                                </tr>
                                @if($farmer->phone)
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $farmer->phone }}</td>
                                </tr>
                                @endif
                                @if($farmer->village)
                                <tr>
                                    <th>Village:</th>
                                    <td>{{ $farmer->village->name ?? 'N/A' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- Training Summary --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Training Summary</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $totalRegistered = $attendances->total();
                                $totalAttended = $attendances->filter(fn($a) => $a->attended)->count();
                                $totalCompleted = $attendances->filter(fn($a) => $a->completed)->count();
                                $totalCertificates = $attendances->filter(fn($a) => $a->certificate_issued)->count();
                            @endphp
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <h4 class="text-primary">{{ $totalRegistered }}</h4>
                                    <small class="text-muted">Registered</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-success">{{ $totalAttended }}</h4>
                                    <small class="text-muted">Attended</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info">{{ $totalCompleted }}</h4>
                                    <small class="text-muted">Completed</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-warning">{{ $totalCertificates }}</h4>
                                    <small class="text-muted">Certificates</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Training History --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i> Training Records</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Training</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Attendance</th>
                                            <th>Certificate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendances as $attendance)
                                            <tr>
                                                <td>
                                                    <strong>{{ $attendance->session->title ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <span class="badge bg-info">{{ $attendance->session->program->code ?? '' }}</span>
                                                        {{ $attendance->session->program->name ?? 'N/A' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if($attendance->session)
                                                        {{ $attendance->session->scheduled_date->format('M d, Y') }}
                                                        <br><small class="text-muted">{{ $attendance->session->venue ?? '' }}</small>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $attendance->registration_status == 'registered' ? 'success' : ($attendance->registration_status == 'waitlist' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($attendance->registration_status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($attendance->attended)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i> Attended
                                                        </span>
                                                        @if($attendance->check_in_time)
                                                            <br><small class="text-muted">
                                                                In: {{ $attendance->check_in_time->format('H:i') }}
                                                                @if($attendance->check_out_time)
                                                                    - Out: {{ $attendance->check_out_time->format('H:i') }}
                                                                @endif
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">Not Attended</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attendance->certificate_issued)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-certificate me-1"></i> Issued
                                                        </span>
                                                        <br><small class="text-muted">{{ $attendance->certificate_number }}</small>
                                                    @else
                                                        <span class="badge bg-secondary">Not Issued</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <i class="fas fa-graduation-cap fa-2x mb-2"></i>
                                                    <p>No training records found for this farmer</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if($attendances->hasPages())
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted">
                                            Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} records
                                        </div>
                                        <div>
                                            {{ $attendances->links() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
