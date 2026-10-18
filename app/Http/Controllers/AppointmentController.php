<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor'])->latest('appointment_date')->paginate(15);

        return view('resources.index', [
            'title' => 'Appointments',
            'resourceName' => 'appointments',
            'records' => $appointments,
            'columns' => [
                ['label' => 'Date', 'key' => 'appointment_date'],
                ['label' => 'Time', 'key' => 'appointment_time'],
                ['label' => 'Patient', 'key' => 'patient.name'],
                ['label' => 'Doctor', 'key' => 'doctor.name'],
                ['label' => 'Status', 'key' => 'status'],
            ],
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        Appointment::create($request->validate($this->rules()));

        return redirect()->route('appointments.index')->with('success', 'Appointment scheduled successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'medicalRecord']);

        return view('resources.show', [
            'title' => 'Appointment details',
            'resourceName' => 'appointments',
            'resource' => $appointment,
            'details' => [
                'Date' => $appointment->appointment_date->format('M j, Y'),
                'Time' => substr($appointment->appointment_time, 0, 5),
                'Patient' => $appointment->patient->name,
                'Doctor' => $appointment->doctor->name,
                'Status' => ucfirst($appointment->status),
                'Reason' => $appointment->reason,
                'Medical record' => $appointment->medicalRecord ? route('medical-records.show', $appointment->medicalRecord) : 'Not recorded',
            ],
        ]);
    }

    public function edit(Appointment $appointment)
    {
        return $this->form($appointment);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $appointment->update($request->validate($this->rules()));

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->medicalRecord()->exists()) {
            return back()->with('error', 'This appointment has a medical record and cannot be deleted.');
        }

        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }

    private function form(?Appointment $appointment = null)
    {
        return view('resources.form', [
            'title' => $appointment ? 'Edit appointment' : 'Schedule appointment',
            'resourceName' => 'appointments',
            'resource' => $appointment,
            'fields' => [
                ['name' => 'patient_id', 'label' => 'Patient', 'type' => 'select', 'options' => Patient::orderBy('name')->pluck('name', 'id')->all(), 'required' => true],
                ['name' => 'doctor_id', 'label' => 'Doctor', 'type' => 'select', 'options' => Doctor::orderBy('name')->pluck('name', 'id')->all(), 'required' => true],
                ['name' => 'appointment_date', 'label' => 'Date', 'type' => 'date', 'required' => true, 'value' => $appointment?->getRawOriginal('appointment_date')],
                ['name' => 'appointment_time', 'label' => 'Time', 'type' => 'time', 'required' => true, 'value' => $appointment ? substr($appointment->getRawOriginal('appointment_time'), 0, 5) : null],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'], 'required' => true],
                ['name' => 'reason', 'label' => 'Reason for visit', 'type' => 'textarea'],
            ],
        ]);
    }

    private function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'status' => ['required', 'in:scheduled,completed,cancelled'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
