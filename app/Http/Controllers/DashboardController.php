<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $counts = [
            'doctors' => Doctor::count(),
            'patients' => Patient::count(),
            'appointments' => Appointment::count(),
            'medicalRecords' => MedicalRecord::count(),
            'medicines' => Medicine::count(),
        ];

        $upcomingAppointments = Appointment::with(['patient', 'doctor'])
            ->where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', today())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(6)
            ->get();

        return view('dashboard', compact('counts', 'upcomingAppointments'));
    }
}
