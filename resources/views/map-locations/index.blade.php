@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-map-location-dot text-primary me-2"></i>
                Map Locations
            </h2>
            <p class="text-muted mb-0">Kelola lokasi Rumah Client, ODP, dan Server di peta.</p>
        </div>
        <a href="{{ route('map-locations.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus me-2"></i>Tambah Lokasi
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Daftar Lokasi</h5>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Type</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mapLocations as $location)
                                    <tr>
                                        <td>{{ $location->name }}</td>
                                        <td>{{ $location->type_label }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('map-locations.edit', $location) }}" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                            <form action="{{ route('map-locations.destroy', $location) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus lokasi {{ $location->name }}?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Belum ada lokasi peta. Tambahkan lokasi ODP atau Server terlebih dahulu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title">Rumah Client dengan koordinat</h5>
                    <p class="text-muted">Lokasi pelanggan ditampilkan kalau sudah diisi latitude/longitude di halaman Customer.</p>
                    <ul class="list-group list-group-flush">
                        @forelse($customers as $customer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $customer->name }}</span>
                                <span class="badge bg-primary">Client</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Belum ada rumah client dengan koordinat.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div id="map" style="min-height: 620px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-o9N1j7gY3Qzc5d3fKe9gZQws8yCkRb8ff3VV5VQvS7w=" crossorigin=""/>
<style>
    #map { min-height: 620px; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-o6EFH836Y/GlTvz5LjG3W9CJxxAaW8F6f5hRk4ARm6M=" crossorigin=""></script>
<script>
    const map = L.map('map').setView([-6.200000, 106.816666], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    const locationData = @json($mapLocations->map(function ($location) {
        return [
            'type' => $location->type,
            'name' => $location->name,
            'address' => $location->address,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'type_label' => $location->type_label,
            'active' => $location->active,
        ];
    }));

    const customerData = @json($customers->map(function ($customer) {
        return [
            'name' => $customer->name,
            'username' => $customer->username,
            'latitude' => $customer->latitude,
            'longitude' => $customer->longitude,
            'type' => 'client',
        ];
    }));

    function markerOptions(type) {
        switch (type) {
            case 'odp': return { color: '#ff7f00', fillColor: '#ff7f00', radius: 9 };
            case 'server': return { color: '#d00000', fillColor: '#d00000', radius: 9 };
            default: return { color: '#0d6efd', fillColor: '#0d6efd', radius: 9 };
        }
    }

    let bounds = [];

    locationData.forEach(point => {
        if (point.latitude && point.longitude) {
            const marker = L.circleMarker([point.latitude, point.longitude], markerOptions(point.type)).addTo(map);
            marker.bindPopup(`<strong>${point.name}</strong><br>${point.type_label}<br>${point.address ?? ''}`);
            bounds.push([point.latitude, point.longitude]);
        }
    });

    customerData.forEach(point => {
        if (point.latitude && point.longitude) {
            const marker = L.circleMarker([point.latitude, point.longitude], markerOptions('client')).addTo(map);
            marker.bindPopup(`<strong>${point.name}</strong><br>Client House<br>${point.username}`);
            bounds.push([point.latitude, point.longitude]);
        }
    });

    if (bounds.length) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }
</script>
@endpush
