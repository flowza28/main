@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Edit Lokasi Peta</h3>
        <form method="POST" action="{{ route('map-locations.update', $mapLocation) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tipe Lokasi</label>
                    <select name="type" class="form-select" required>
                        <option value="">Pilih tipe</option>
                        @foreach(\App\Models\MapLocation::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $mapLocation->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Lokasi</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $mapLocation->name) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control">{{ old('address', $mapLocation->address) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" id="latitude" class="form-control" value="{{ old('latitude', $mapLocation->latitude) }}" placeholder="-6.200000">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" id="longitude" class="form-control" value="{{ old('longitude', $mapLocation->longitude) }}" placeholder="106.816666">
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control">{{ old('notes', $mapLocation->notes) }}</textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="active" value="1" class="form-check-input" {{ old('active', $mapLocation->active) ? 'checked' : '' }}>
                        <label class="form-check-label">Aktif</label>
                    </div>
                </div>
                <div class="col-12">
                    <div id="location-map" style="min-height: 420px;"></div>
                    <small class="text-muted">Klik peta untuk mengubah koordinat lokasi.</small>
                </div>
            </div>
            <button class="btn btn-primary mt-3">Update Lokasi</button>
            <a href="{{ route('map-locations.index') }}" class="btn btn-secondary mt-3 ms-2">Kembali</a>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-o9N1j7gY3Qzc5d3fKe9gZQws8yCkRb8ff3VV5VQvS7w=" crossorigin=""/>
<style>
    #location-map { width: 100%; height: 420px; margin-top: 1rem; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-o6EFH836Y/GlTvz5LjG3W9CJxxAaW8F6f5hRk4ARm6M=" crossorigin=""></script>
<script>
    const map = L.map('location-map').setView([{{ old('latitude', $mapLocation->latitude) ?? '-6.200000' }}, {{ old('longitude', $mapLocation->longitude) ?? '106.816666' }}], {{ $mapLocation->latitude && $mapLocation->longitude ? 15 : 11 }});
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors', maxZoom: 19,
    }).addTo(map);

    const marker = L.marker([{{ old('latitude', $mapLocation->latitude) ?? '-6.200000' }}, {{ old('longitude', $mapLocation->longitude) ?? '106.816666' }}]).addTo(map);
    @if(old('latitude', $mapLocation->latitude) && old('longitude', $mapLocation->longitude))
        marker.setLatLng([{{ old('latitude', $mapLocation->latitude) }}, {{ old('longitude', $mapLocation->longitude) }}]);
    @else
        map.removeLayer(marker);
    @endif

    function updateMarker(lat, lng) {
        if (!lat || !lng) return;
        if (!map.hasLayer(marker)) {
            marker.addTo(map);
        }
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);
    }

    map.on('click', (event) => {
        const lat = event.latlng.lat.toFixed(7);
        const lng = event.latlng.lng.toFixed(7);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        updateMarker(lat, lng);
    });
</script>
@endpush
