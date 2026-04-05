@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h3>Tambah Paket</h3>
        <form method="POST" action="{{ route('packages.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Paket</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Download (Kbps)</label>
                    <input type="number" name="download_speed" class="form-control" value="{{ old('download_speed') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Upload (Kbps)</label>
                    <input type="number" name="upload_speed" class="form-control" value="{{ old('upload_speed') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>
            </div>
            <button class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
</div>
@endsection
