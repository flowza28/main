@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Customer ISP</h3>
    <div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">Tambah Customer</a>
    </div>
</div>

<div class="mb-3">
    <form method="GET" class="row g-2" action="{{ route('customers.index') }}">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama atau username">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary">Cari</button>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>Username</th>
                <th>Nama</th>
                <th>Paket</th>
                <th>Expired</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->username }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->package->name ?? '-' }}</td>
                    <td>{{ optional($customer->expired_at)->format('Y-m-d') }}</td>
                    <td>{{ $customer->status }}</td>
                    <td>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('customers.toggleStatus', $customer) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-secondary">{{ $customer->active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus customer?');">
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

{{ $customers->links() }}
@endsection
