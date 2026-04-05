@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Invoices</h3>
    <div>
        <a href="{{ route('invoices.generate') }}" class="btn btn-success">Generate Tagihan Bulanan</a>
    </div>
</div>

<div class="mb-3">
    <form method="GET" class="row g-2" action="{{ route('invoices.index') }}">
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary">Filter</button>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No Invoice</th>
                <th>Customer</th>
                <th>Jumlah</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    <td>{{ $invoice->due_date->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($invoice->status) }}</td>
                    <td>
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-secondary">PDF</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $invoices->links() }}
@endsection
