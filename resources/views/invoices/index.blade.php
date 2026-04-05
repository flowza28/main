@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                Invoice Management
            </h2>
            <p class="text-muted mb-0">Manage customer invoices and billing</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('invoices.generate') }}" class="btn btn-success d-flex align-items-center">
                <i class="fas fa-plus me-2"></i>Generate Monthly Bills
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice-dollar fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1">{{ $invoices->total() }}</h4>
                    <small class="text-muted">Total Invoices</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">{{ $invoices->where('status', 'belum')->count() }}</h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">{{ $invoices->where('status', 'lunas')->count() }}</h4>
                    <small class="text-muted">Paid</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">Rp {{ number_format($invoices->where('status', 'lunas')->sum('amount'), 0, ',', '.') }}</h4>
                    <small class="text-muted">Total Revenue</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3" action="{{ route('invoices.index') }}">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-filter me-1"></i>Status Filter
                    </label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Pending</option>
                        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search me-1"></i>Search Invoice
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by invoice number or customer...">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-outline-primary me-2">
                        <i class="fas fa-filter me-1"></i>Apply Filter
                    </button>
                    @if(request('status') || request('search'))
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list text-primary me-2"></i>
                Invoices List
                <span class="badge bg-primary ms-2">{{ $invoices->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Invoice</th>
                            <th class="border-0">Customer</th>
                            <th class="border-0">Amount</th>
                            <th class="border-0">Due Date</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="ps-4">
                                    <div>
                                        <h6 class="mb-0">{{ $invoice->invoice_number }}</h6>
                                        <small class="text-muted">Created: {{ $invoice->created_at->format('d M Y') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $invoice->customer->name }}</h6>
                                            <small class="text-muted">{{ $invoice->customer->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $invoice->due_date->format('d M Y') }}</span>
                                    @if($invoice->due_date->isPast() && $invoice->status === 'belum')
                                        <br><small class="text-danger">Overdue</small>
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->status === 'lunas')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Paid
                                        </span>
                                    @else
                                        <span class="badge bg-warning px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-secondary" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice-dollar fa-3x mb-3"></i>
                                        <h5>No invoices found</h5>
                                        <p>{{ request('status') || request('search') ? 'Try adjusting your search criteria.' : 'Generate monthly bills to create invoices.' }}</p>
                                        @if(!request('status') && !request('search'))
                                            <a href="{{ route('invoices.generate') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>Generate Bills
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($invoices->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
