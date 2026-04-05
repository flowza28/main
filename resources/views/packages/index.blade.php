@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-wifi text-primary me-2"></i>
                Internet Packages
            </h2>
            <p class="text-muted mb-0">Manage ISP service packages and pricing</p>
        </div>
        <a href="{{ route('packages.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus me-2"></i>Add Package
        </a>
    </div>

    <!-- Packages Grid -->
    <div class="row">
        @forelse($packages as $package)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1">{{ $package->name }}</h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1">
                                    <i class="fas fa-wifi me-1"></i>Internet Package
                                </span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('packages.edit', $package) }}">
                                        <i class="fas fa-edit me-2"></i>Edit Package
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('packages.destroy', $package) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" onclick="return confirm('Hapus paket {{ $package->name }}?')">
                                                <i class="fas fa-trash me-2"></i>Delete Package
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Speed Information -->
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="speed-indicator">
                                    <i class="fas fa-download text-success mb-2" style="font-size: 1.5rem;"></i>
                                    <div class="fw-bold text-success">{{ format_bandwidth($package->download_speed) }}</div>
                                    <small class="text-muted">Download</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="speed-indicator">
                                    <i class="fas fa-upload text-info mb-2" style="font-size: 1.5rem;"></i>
                                    <div class="fw-bold text-info">{{ format_bandwidth($package->upload_speed) }}</div>
                                    <small class="text-muted">Upload</small>
                                </div>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="text-center mb-3">
                            <div class="price-display">
                                <span class="h4 text-primary fw-bold mb-0">
                                    Rp {{ number_format($package->price, 0, ',', '.') }}
                                </span>
                                <small class="text-muted d-block">per month</small>
                            </div>
                        </div>

                        <!-- Rate Limit -->
                        @if($package->rate_limit)
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tachometer-alt text-warning me-2"></i>
                                    <span class="small fw-medium">Rate Limit: {{ $package->rate_limit }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Additional Info -->
                        <div class="small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Package ID: {{ $package->id }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-wifi fa-3x mb-3"></i>
                            <h5>No packages found</h5>
                            <p>Start by creating your first internet package.</p>
                            <a href="{{ route('packages.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create First Package
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($packages->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $packages->links() }}
        </div>
    @endif
</div>

<style>
.speed-indicator {
    padding: 1rem;
    border-radius: 0.5rem;
    background: rgba(0,0,0,0.02);
}

.price-display {
    padding: 1rem;
    border-radius: 0.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.price-display small {
    color: rgba(255,255,255,0.8);
}
</style>
@endsection
