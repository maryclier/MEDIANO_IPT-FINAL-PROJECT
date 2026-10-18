<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $records = MedicalRecord::with(['appointment.patient', 'appointment.doctor', 'medicine'])->latest()->paginate(15);

        return view('resources.index', [
            'title' => 'Medical records',
            'resourceName' => 'medical-records',
            'records' => $records,
            'columns' => [
                ['label' => 'Visit date', 'key' => 'appointment.appointment_date'],
                ['label' => 'Patient', 'key' => 'appointment.patient.name'],
                ['label' => 'Doctor', 'key' => 'appointment.doctor.name'],
                ['label' => 'Diagnosis', 'key' => 'diagnosis'],
                ['label' => 'Medicine', 'key' => 'medicine.name'],
            ],
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        MedicalRecord::create($request->validate($this->rules()));

        return redirect()->route('medical-records.index')->with('success', 'Medical record saved successfully.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['appointment.patient', 'appointment.doctor', 'medicine']);

        return view('resources.show', [
            'title' => 'Medical record details',
            'resourceName' => 'medical-records',
            'resource' => $medicalRecord,
            'details' => [
                'Visit date' => $medicalRecord->appointment->appointment_date->format('M j, Y'),
                'Patient' => $medicalRecord->appointment->patient->name,
                'Doctor' => $medicalRecord->appointment->doctor->name,
                'Diagnosis' => $medicalRecord->diagnosis,
                'Treatment' => $medicalRecord->treatment,
                'Medicine' => $medicalRecord->medicine?->name,
                'Dosage' => $medicalRecord->dosage,
                'Notes' => $medicalRecord->notes,
            ],
        ]);
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        return $this->form($medicalRecord);
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $medicalRecord->update($request->validate($this->rules($medicalRecord)));

        return redirect()->route('medical-records.index')->with('success', 'Medical record updated successfully.');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();

        return redirect()->route('medical-records.index')->with('success', 'Medical record deleted successfully.');
    }

    private function form(?MedicalRecord $medicalRecord = null)
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereDoesntHave('medicalRecord')
            ->when($medicalRecord, fn ($query) => $query->orWhereKey($medicalRecord->appointment_id))
            ->orderByDesc('appointment_date')
            ->get()
            ->mapWithKeys(fn (Appointment $appointment) => [
                $appointment->id => $appointment->appointment_date->format('M j, Y').' - '.$appointment->patient->name.' / '.$appointment->doctor->name,
            ])
            ->all();

        return view('resources.form', [
            'title' => $medicalRecord ? 'Edit medical record' : 'Add medical record',
            'resourceName' => 'medical-records',
            'resource' => $medicalRecord,
            'fields' => [
                ['name' => 'appointment_id', 'label' => 'Appointment', 'type' => 'select', 'options' => $appointments, 'required' => true],
                ['name' => 'diagnosis', 'label' => 'Diagnosis', 'type' => 'textarea', 'required' => true],
                ['name' => 'treatment', 'label' => 'Treatment', 'type' => 'textarea'],
                ['name' => 'medicine_id', 'label' => 'Medicine (optional)', 'type' => 'select', 'options' => Medicine::orderBy('name')->pluck('name', 'id')->all()],
                ['name' => 'dosage', 'label' => 'Dosage / instructions'],
                ['name' => 'notes', 'label' => 'Additional notes', 'type' => 'textarea'],
            ],
        ]);
    }

    private function rules(?MedicalRecord $medicalRecord = null): array
    {
        return [
            'appointment_id' => [
                'required',
                'exists:appointments,id',
                Rule::unique('medical_records', 'appointment_id')->ignore($medicalRecord?->id),
            ],
            'medicine_id' => ['nullable', 'exists:medicines,id'],
            'diagnosis' => ['required', 'string', 'max:5000'],
            'treatment' => ['nullable', 'string', 'max:5000'],
            'dosage' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
