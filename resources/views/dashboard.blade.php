@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h1 class="h2 page-title mb-1">School clinic dashboard</h1>
        <p class="text-secondary mb-0">A quick overview of clinic activity and records.</p>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['label' => 'Doctors', 'value' => $counts['doctors'], 'route' => 'doctors.index'],
            ['label' => 'Patients', 'value' => $counts['patients'], 'route' => 'patients.index'],
            ['label' => 'Appointments', 'value' => $counts['appointments'], 'route' => 'appointments.index'],
            ['label' => 'Medical records', 'value' => $counts['medicalRecords'], 'route' => 'medical-records.index'],
            ['label' => 'Medicines', 'value' => $counts['medicines'], 'route' => 'medicines.index'],
        ] as $card)
            <div class="col-6 col-lg">
                <a class="card h-100 text-decoration-none text-reset" href="{{ route($card['route']) }}">
                    <div class="card-body">
                        <div class="small text-secondary">{{ $card['label'] }}</div>
                        <div class="display-6 fw-semibold">{{ $card['value'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 page-title mb-0">Upcoming appointments</h2>
        <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-primary">Schedule appointment</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Date</th><th>Time</th><th>Patient</th><th>Doctor</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($upcomingAppointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_date->format('M j, Y') }}</td>
                            <td>{{ substr($appointment->appointment_time, 0, 5) }}</td>
                            <td>{{ $appointment->patient->name }}</td>
                            <td>{{ $appointment->doctor->name }}</td>
                            <td><span class="badge text-bg-primary">{{ ucfirst($appointment->status) }}</span></td>
                            <td class="text-end"><a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-4 text-center text-secondary">No upcoming appointments.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
