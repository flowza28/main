@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-users text-primary fs-4"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="card-title mb-0 text-muted">Total Users</h6>
                            <h3 class="mb-0 text-primary">{{ $totalUsers }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-user-check text-success fs-4"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="card-title mb-0 text-muted">Active Users</h6>
                            <h3 class="mb-0 text-success">{{ $activeUsers }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-chart-line text-warning fs-4"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="card-title mb-0 text-muted">Total Bandwidth</h6>
                            <h4 class="mb-0 text-warning">{{ format_bytes($totalUsage) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-dollar-sign text-info fs-4"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="card-title mb-0 text-muted">Revenue This Month</h6>
                            <h4 class="mb-0 text-info">Rp {{ number_format($currentMonthRevenue, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Traffic Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px; width: 100%;">
                        <canvas id="dashboardChart"></canvas>
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
    const ctx = document.getElementById('dashboardChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json(array_column($bandwidthUsage, 'username')),
                datasets: [
                    {
                        label: 'Download (Bytes)',
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: '#0d6efd',
                        borderWidth: 1,
                        borderRadius: 4,
                        data: @json(array_column($bandwidthUsage, 'download')),
                    },
                    {
                        label: 'Upload (Bytes)',
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                        borderColor: '#198754',
                        borderWidth: 1,
                        borderRadius: 4,
                        data: @json(array_column($bandwidthUsage, 'upload')),
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
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
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 4
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
