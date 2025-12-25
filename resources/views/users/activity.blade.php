@extends('layouts.base')

@section('title', 'Activity Log - ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-history me-2"></i> Activity Log: {{ $user->name }}
                </h4>
                <a href="{{ route('users.show', $user) }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-1"></i> Back to Profile
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($activities->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Action</th>
                                <th>Performed By</th>
                                <th>IP Address</th>
                                <th>Details</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <div class="bg-{{ $activity->action_color }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas {{ $activity->action_icon }}"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $activity->action_label }}</strong>
                                    </td>
                                    <td>
                                        @if($activity->performedBy)
                                            {{ $activity->performedBy->name }}
                                            @if($activity->performedBy->id === $user->id)
                                                <span class="badge bg-secondary">Self</span>
                                            @endif
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activity->ip_address)
                                            <code>{{ $activity->ip_address }}</code>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activity->details)
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    data-bs-toggle="popover"
                                                    data-bs-trigger="hover"
                                                    data-bs-html="true"
                                                    data-bs-content="<pre class='mb-0'>{{ json_encode($activity->details, JSON_PRETTY_PRINT) }}</pre>">
                                                <i class="fas fa-info-circle"></i> View
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span title="{{ $activity->created_at->format('Y-m-d H:i:s') }}">
                                            {{ $activity->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $activity->created_at->format('H:i:s') }}</small>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($activities->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted">
                            Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} activities
                        </span>
                        {{ $activities->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-4x text-muted mb-3"></i>
                    <h5>No activity recorded</h5>
                    <p class="text-muted">There is no activity history for this user yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })
</script>
@endpush
@endsection
