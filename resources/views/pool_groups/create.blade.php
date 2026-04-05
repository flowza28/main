@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Add Pool Address
                    </h2>
                    <p class="text-muted mb-0">Create a new pool group for customer assignment.</p>
                </div>
                <a href="{{ route('pool-groups.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Pool List
                </a>
            </div>

            <form method="POST" action="{{ route('pool-groups.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Pool</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start IP</label>
                        <input type="text" name="start_ip" class="form-control" value="{{ old('start_ip') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End IP</label>
                        <input type="text" name="end_ip" class="form-control" value="{{ old('end_ip') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    </div>
                </div>

                <button class="btn btn-primary mt-4">
                    <i class="fas fa-save me-1"></i>Save Pool Address
                </button>
            </form>
        </div>
    </div>
</div>
@endsection