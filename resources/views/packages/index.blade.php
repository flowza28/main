@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Paket Internet</h3>
    <a href="{{ route('packages.create') }}" class="btn btn-primary">Tambah Paket</a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Download</th>
                <th>Upload</th>
                <th>Harga</th>
                <th>Rate Limit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($packages as $package)
                <tr>
                    <td>{{ $package->name }}</td>
                    <td>{{ format_bandwidth($package->download_speed) }}</td>
                    <td>{{ format_bandwidth($package->upload_speed) }}</td>
                    <td>Rp {{ number_format($package->price, 0, ',', '.') }}</td>
                    <td>{{ $package->rate_limit }}</td>
                    <td>
                        <a href="{{ route('packages.edit', $package) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('packages.destroy', $package) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus paket?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $packages->links() }}
@endsection
