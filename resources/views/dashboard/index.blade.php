@extends('layouts.app')

@section('content')
<div class="row gy-3">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5>Total Users</h5>
                <h2>{{ $totalUsers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5>Active Users</h5>
                <h2>{{ $activeUsers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5>Total Bandwidth</h5>
                <h2>{{ format_bytes($totalUsage) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5>Revenue This Month</h5>
                <h2>Rp {{ number_format($currentMonthRevenue, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">Traffic Summary</h5>
        <canvas id="dashboardChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('dashboardChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(array_column($bandwidthUsage, 'username')),
            datasets: [
                {
                    label: 'Download',
                    backgroundColor: '#0d6efd',
                    data: @json(array_column($bandwidthUsage, 'download')),
                },
                {
                    label: 'Upload',
                    backgroundColor: '#198754',
                    data: @json(array_column($bandwidthUsage, 'upload')),
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>
@endpush
