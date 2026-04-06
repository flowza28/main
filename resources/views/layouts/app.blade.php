<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ISP Billing') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }
        .avatar {
            width: 40px;
            height: 40px;
        }
        .avatar-sm {
            width: 32px;
            height: 32px;
        }
        .chart-container {
            position: relative;
            margin: auto;
        }
        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <i class="fas fa-wifi text-warning me-2"></i>
            <span class="fw-bold">ISP Billing</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            @auth
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('customers.*') ? 'active fw-bold' : '' }}" href="{{ route('customers.index') }}">
                        <i class="fas fa-users me-1"></i>Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('packages.*') ? 'active fw-bold' : '' }}" href="{{ route('packages.index') }}">
                        <i class="fas fa-box me-1"></i>Packages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('invoices.*') ? 'active fw-bold' : '' }}" href="{{ route('invoices.index') }}">
                        <i class="fas fa-file-invoice-dollar me-1"></i>Invoices
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('monitoring.*') ? 'active fw-bold' : '' }}" href="{{ route('monitoring.index') }}">
                        <i class="fas fa-chart-line me-1"></i>Monitoring
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('map-locations.*') ? 'active fw-bold' : '' }}" href="{{ route('map-locations.index') }}">
                        <i class="fas fa-map-location-dot me-1"></i>Map
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->name }}
                        <span class="badge bg-light text-primary ms-1">{{ auth()->user()->role }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
            @endauth
        </div>
    </div>
</nav>

<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
