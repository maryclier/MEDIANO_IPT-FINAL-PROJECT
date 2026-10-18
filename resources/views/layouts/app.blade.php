<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} | School Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fb; color: #172b4d; }
        .navbar { background: #123c69; }
        .navbar-brand, .navbar .nav-link { color: #fff; }
        .navbar .nav-link { opacity: .82; }
        .navbar .nav-link:hover, .navbar .nav-link.active { color: #fff; opacity: 1; }
        .card { border: 0; box-shadow: 0 .25rem 1rem rgba(31, 55, 84, .07); }
        .page-title { color: #123c69; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">School Clinic</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavigation">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}" href="{{ route('doctors.index') }}">Doctors</a>
                    <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}">Patients</a>
                    <a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">Appointments</a>
                    <a class="nav-link {{ request()->routeIs('medical-records.*') ? 'active' : '' }}" href="{{ route('medical-records.index') }}">Medical records</a>
                    <a class="nav-link {{ request()->routeIs('medicines.*') ? 'active' : '' }}" href="{{ route('medicines.index') }}">Medicines</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-lg-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
