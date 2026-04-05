@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-chart-line text-primary me-2"></i>
                User Monitoring
            </h2>
            <p class="text-muted mb-0">Monitor user traffic and connection status</p>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex" action="{{ route('monitoring.index') }}">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input name="search" value="{{ $search }}" class="form-control" placeholder="Search username..." style="min-width: 200px;">
                    <button class="btn btn-outline-primary">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-circle text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 text-muted">Online Users</h6>
                            <h3 class="mb-0 text-success">{{ $onlineUsers }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-circle text-secondary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 text-muted">Offline Users</h6>
                            <h3 class="mb-0 text-secondary">{{ $offlineUsers }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-users text-info fs-4"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-0 text-muted">Total Users</h6>
                            <h3 class="mb-0 text-info">{{ $onlineUsers + $offlineUsers }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-wave-square text-primary me-2"></i>
                        Traffic Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                        <canvas id="trafficChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Traffic Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table text-primary me-2"></i>
                        User Traffic Details
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-4">Username</th>
                                    <th class="border-0">Download</th>
                                    <th class="border-0">Upload</th>
                                    <th class="border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($traffic as $row)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <span class="fw-medium">{{ $row['username'] }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                                {{ format_bytes($row['download']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                                {{ format_bytes($row['upload']) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($row['online'])
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="fas fa-circle me-1"></i>Online
                                                </span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2">
                                                    <i class="fas fa-circle me-1"></i>Offline
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chart = document.getElementById('trafficChart');
    if (chart) {
        new Chart(chart, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Download',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderColor: '#0d6efd',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0d6efd',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        data: @json($chartData['download'])
                    },
                    {
                        label: 'Upload',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderColor: '#198754',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#198754',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        data: @json($chartData['upload'])
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + formatBytes(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatBytes(value);
                            },
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBorderWidth: 3
                    }
                }
            }
        });
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endpush
