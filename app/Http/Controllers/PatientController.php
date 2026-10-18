<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::withCount('appointments')->latest()->paginate(15);

        return view('resources.index', [
            'title' => 'Patients',
            'resourceName' => 'patients',
            'records' => $patients,
            'columns' => [
                ['label' => 'Name', 'key' => 'name'],
                ['label' => 'Age', 'key' => 'age'],
                ['label' => 'Gender', 'key' => 'gender'],
                ['label' => 'Phone', 'key' => 'phone'],
                ['label' => 'Appointments', 'key' => 'appointments_count'],
            ],
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        Patient::create($request->validate($this->rules()));

        return redirect()->route('patients.index')->with('success', 'Patient added successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->loadCount('appointments');

        return view('resources.show', [
            'title' => 'Patient details',
            'resourceName' => 'patients',
            'resource' => $patient,
            'details' => [
                'Name' => $patient->name,
                'Age' => $patient->age,
                'Gender' => $patient->gender,
                'Address' => $patient->address,
                'Phone' => $patient->phone,
                'Email' => $patient->email,
                'Appointments' => $patient->appointments_count,
            ],
        ]);
    }

    public function edit(Patient $patient)
    {
        return $this->form($patient);
    }

    public function update(Request $request, Patient $patient)
    {
        $patient->update($request->validate($this->rules($patient)));

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        if ($patient->appointments()->exists()) {
            return back()->with('error', 'This patient has appointment history and cannot be deleted.');
        }

        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }

    private function form(?Patient $patient = null)
    {
        return view('resources.form', [
            'title' => $patient ? 'Edit patient' : 'Add patient',
            'resourceName' => 'patients',
            'resource' => $patient,
            'fields' => [
                ['name' => 'name', 'label' => 'Full name', 'required' => true],
                ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'attributes' => ['min' => 0, 'max' => 120]],
                ['name' => 'gender', 'label' => 'Gender', 'type' => 'select', 'options' => ['Female' => 'Female', 'Male' => 'Male', 'Other' => 'Other']],
                ['name' => 'address', 'label' => 'Address', 'type' => 'textarea'],
                ['name' => 'phone', 'label' => 'Phone'],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ],
        ]);
    }

    private function rules(?Patient $patient = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['nullable', 'in:Female,Male,Other'],
            'address' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('patients', 'email')->ignore($patient?->id)],
        ];
    }
}
