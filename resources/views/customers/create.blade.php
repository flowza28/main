@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Tambah Customer</h3>
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="text" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Paket</label>
                    <select name="package_id" class="form-select" required>
                        <option value="">Pilih paket</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }} - {{ format_bandwidth($package->download_speed) }} / {{ format_bandwidth($package->upload_speed) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Expired Date</label>
                    <input type="date" name="expired_at" class="form-control" value="{{ old('expired_at') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-check-label">Aktif</label>
                    <div class="form-check">
                        <input type="checkbox" name="active" class="form-check-input" value="1" checked>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">IP Address</label>
                    <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address') }}" placeholder="192.168.1.1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="-6.200000" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="106.816666" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control">{{ old('address') }}</textarea>
                </div>
            </div>
            <button class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
</div>
@endsection
