@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Edit Customer</h3>
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $customer->username) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password (biarkan kosong jika tidak diubah)</label>
                    <input type="text" name="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Paket</label>
                    <select name="package_id" class="form-select" required>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ $customer->package_id === $package->id ? 'selected' : '' }}>{{ $package->name }} - {{ format_bandwidth($package->download_speed) }} / {{ format_bandwidth($package->upload_speed) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pool Group</label>
                    <select name="pool_group_id" class="form-select">
                        <option value="">Pilih pool group (opsional)</option>
                        @foreach($poolGroups as $poolGroup)
                            <option value="{{ $poolGroup->id }}" {{ $customer->pool_group_id === $poolGroup->id ? 'selected' : '' }}>{{ $poolGroup->name }} ({{ $poolGroup->start_ip }} - {{ $poolGroup->end_ip }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Expired Date</label>
                    <input type="date" name="expired_at" class="form-control" value="{{ old('expired_at', optional($customer->expired_at)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Aktif</label>
                    <div class="form-check">
                        <input type="checkbox" name="active" class="form-check-input" value="1" {{ $customer->active ? 'checked' : '' }}>
                        <label class="form-check-label">Aktif</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $customer->latitude) }}" placeholder="-6.200000" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $customer->longitude) }}" placeholder="106.816666" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control">{{ old('address', $customer->address) }}</textarea>
                </div>
            </div>
            <button class="btn btn-primary mt-3">Update</button>
        </form>
    </div>
</div>
@endsection
