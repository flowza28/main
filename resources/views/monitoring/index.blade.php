@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Monitoring User</h3>
    <form method="GET" class="d-flex" action="{{ route('monitoring.index') }}">
        <input name="search" value="{{ $search }}" class="form-control me-2" placeholder="Cari username...">
        <button class="btn btn-outline-secondary">Search</button>
    </form>
</div>

<div class="row gy-3">
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body">
                <h5>Online</h5>
                <h2>{{ $onlineUsers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-secondary">
            <div class="card-body">
                <h5>Offline</h5>
                <h2>{{ $offlineUsers }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5>User Traffic</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Download</th>
                        <th>Upload</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($traffic as $row)
                        <tr>
                            <td>{{ $row['username'] }}</td>
                            <td>{{ format_bytes($row['download']) }}</td>
                            <td>{{ format_bytes($row['upload']) }}</td>
                            <td>{{ $row['online'] ? 'Online' : 'Offline' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <canvas id="trafficChart" height="120"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chart = document.getElementById('trafficChart');
if (chart) {
    new Chart(chart, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                { label: 'Download', backgroundColor: 'rgba(13,110,253,0.2)', borderColor: '#0d6efd', data: @json($chartData['download']), tension: 0.3 },
                { label: 'Upload', backgroundColor: 'rgba(25,135,84,0.2)', borderColor: '#198754', data: @json($chartData['upload']), tension: 0.3 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
}
</script>
@endpush
