<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h2>Invoice</h2>
            <p>{{ $invoice->invoice_number }}</p>
        </div>
        <div>
            <p>Tanggal: {{ now()->format('Y-m-d') }}</p>
            <p>Status: {{ ucfirst($invoice->status) }}</p>
        </div>
    </div>

    <p><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
    <table class="table mb-4">
        <tr><th>Deskripsi</th><th>Jumlah</th></tr>
        <tr><td>{{ $invoice->description }}</td><td>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td></tr>
    </table>

    <p><strong>Due Date:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>
</body>
</html>
