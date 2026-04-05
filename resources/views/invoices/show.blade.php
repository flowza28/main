@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>Invoice {{ $invoice->invoice_number }}</h3>
                <p class="mb-0">Customer: {{ $invoice->customer->name }}</p>
            </div>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary">Download PDF</a>
        </div>

        <table class="table table-borderless">
            <tr><th>Invoice Number</th><td>{{ $invoice->invoice_number }}</td></tr>
            <tr><th>Amount</th><td>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td></tr>
            <tr><th>Due Date</th><td>{{ $invoice->due_date->format('Y-m-d') }}</td></tr>
            <tr><th>Status</th><td>{{ ucfirst($invoice->status) }}</td></tr>
            <tr><th>Description</th><td>{{ $invoice->description }}</td></tr>
        </table>

        @if($invoice->status === 'belum')
            <div class="card mt-3">
                <div class="card-body">
                    <h5>Bayar Sekarang</h5>
                    <form method="POST" action="{{ route('payments.store', $invoice) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="amount" class="form-control" value="{{ $invoice->amount }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Metode</label>
                                <input type="text" name="method" class="form-control" value="transfer" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Bayar</label>
                                <input type="date" name="paid_at" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea name="note" class="form-control"></textarea>
                            </div>
                        </div>
                        <button class="btn btn-success mt-3">Simpan Pembayaran</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
