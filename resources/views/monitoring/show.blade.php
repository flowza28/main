@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-user text-primary me-2"></i>
                User Traffic Detail: {{ $userTraffic['username'] }}
            </h2>
            <p class="text-muted mb-0">Detailed traffic and session history</p>
        </div>
        <a href="{{ route('monitoring.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Monitoring
        </a>
    </div>

    @if(isset($error))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ $error }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-wave-square text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 text-muted">Total Sessions</h6>
                            <h3 class="mb-0 text-primary">{{ $userTraffic['total_sessions'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-download text-info fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 text-muted">Total Download</h6>
                            <h3 class="mb-0 text-info">{{ $userTraffic['total_download'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-upload text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 text-muted">Total Upload</h6>
                            <h3 class="mb-0 text-success">{{ $userTraffic['total_upload'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-chart-pie text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 text-muted">Total Traffic</h6>
                            <h3 class="mb-0 text-warning">{{ $userTraffic['total_traffic'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-primary me-2"></i>
                        Session History
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Start Time</th>
                                    <th class="border-0">Stop Time</th>
                                    <th class="border-0">Duration</th>
                                    <th class="border-0">Download</th>
                                    <th class="border-0">Upload</th>
                                    <th class="border-0">Total</th>
                                    <th class="border-0">NAS IP</th>
                                    <th class="border-0">Framed IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userTraffic['sessions'] as $session)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-medium">{{ $session['start_time'] ? $session['start_time']->format('d M Y H:i:s') : '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $session['stop_time'] ? $session['stop_time']->format('d M Y H:i:s') : 'Active' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                                {{ $session['session_time'] ? gmdate('H:i:s', $session['session_time']) : '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                                {{ $session['download_formatted'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                                {{ $session['upload_formatted'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                                {{ $session['total_formatted'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <code class="text-muted">{{ $session['nas_ip'] ?? '-' }}</code>
                                        </td>
                                        <td>
                                            <code class="text-muted">{{ $session['framed_ip'] ?? '-' }}</code>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No session data available for this user.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@endsection