@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-users text-primary me-2"></i>
                Customer Management
            </h2>
            <p class="text-muted mb-0">Manage ISP customers and their accounts</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus me-2"></i>Add Customer
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3" action="{{ route('customers.index') }}">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-search me-1"></i>Search Customer
                    </label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search by name or username...">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button class="btn btn-outline-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    @if($search)
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list text-primary me-2"></i>
                Customers List
                <span class="badge bg-primary ms-2">{{ $customers->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Customer</th>
                            <th class="border-0">Package</th>
                            <th class="border-0">Expired Date</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $customer->name }}</h6>
                                            <small class="text-muted">{{ $customer->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($customer->package)
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                            {{ $customer->package->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->expired_at)
                                        <span class="fw-medium">{{ $customer->expired_at->format('d M Y') }}</span>
                                    @else
                                        <span class="text-muted">No limit</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->isExpired())
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i>Expired
                                        </span>
                                    @elseif($customer->status === 'active')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-pause-circle me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customers.toggleStatus', $customer) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm {{ $customer->active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $customer->active ? 'Deactivate' : 'Activate' }}"
                                                    onclick="return confirm('{{ $customer->active ? 'Nonaktifkan' : 'Aktifkan' }} customer ini?')">
                                                <i class="fas fa-{{ $customer->active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Hapus customer {{ $customer->name }}?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>No customers found</h5>
                                        <p>{{ $search ? 'Try adjusting your search criteria.' : 'Start by adding your first customer.' }}</p>
                                        @if(!$search)
                                            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>Add First Customer
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
        @if($customers->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
