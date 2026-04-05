@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-layer-group text-primary me-2"></i>
                Pool Address Management
            </h2>
            <p class="text-muted mb-0">Manage IP pools and group assignments.</p>
        </div>
        <a href="{{ route('pool-groups.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus me-2"></i>Add Pool Address
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list text-primary me-2"></i>
                Pool Addresses
                <span class="badge bg-primary ms-2">{{ $poolGroups->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">Name</th>
                            <th class="border-0">IP Range</th>
                            <th class="border-0">Description</th>
                            <th class="border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($poolGroups as $poolGroup)
                        <tr>
                            <td class="ps-4">
                                <div>
                                    <h6 class="mb-0">{{ $poolGroup->name }}</h6>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                    {{ $poolGroup->start_ip }} - {{ $poolGroup->end_ip }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $poolGroup->description ?: '-' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pool-groups.edit', $poolGroup) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pool-groups.destroy', $poolGroup) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Hapus pool address {{ $poolGroup->name }}?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-layer-group fa-3x mb-3"></i>
                                    <h5>No pool addresses found</h5>
                                    <p>Start by creating a new IP pool group.</p>
                                    <a href="{{ route('pool-groups.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add Pool Address
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($poolGroups->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $poolGroups->links() }}
        </div>
        @endif
    </div>
</div>
@endsection