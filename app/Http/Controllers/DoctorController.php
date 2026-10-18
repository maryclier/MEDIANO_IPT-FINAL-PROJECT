<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::withCount('appointments')->latest()->paginate(15);

        return view('resources.index', [
            'title' => 'Doctors',
            'resourceName' => 'doctors',
            'records' => $doctors,
            'columns' => [
                ['label' => 'Name', 'key' => 'name'],
                ['label' => 'Specialization', 'key' => 'specialization'],
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
        Doctor::create($request->validate($this->rules()));

        return redirect()->route('doctors.index')->with('success', 'Doctor added successfully.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->loadCount('appointments');

        return view('resources.show', [
            'title' => 'Doctor details',
            'resourceName' => 'doctors',
            'resource' => $doctor,
            'details' => [
                'Name' => $doctor->name,
                'Specialization' => $doctor->specialization,
                'Phone' => $doctor->phone,
                'Email' => $doctor->email,
                'Appointments' => $doctor->appointments_count,
            ],
        ]);
    }

    public function edit(Doctor $doctor)
    {
        return $this->form($doctor);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $doctor->update($request->validate($this->rules($doctor)));

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->appointments()->exists()) {
            return back()->with('error', 'This doctor has appointment history and cannot be deleted.');
        }

        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully.');
    }

    private function form(?Doctor $doctor = null)
    {
        return view('resources.form', [
            'title' => $doctor ? 'Edit doctor' : 'Add doctor',
            'resourceName' => 'doctors',
            'resource' => $doctor,
            'fields' => [
                ['name' => 'name', 'label' => 'Full name', 'required' => true],
                ['name' => 'specialization', 'label' => 'Specialization'],
                ['name' => 'phone', 'label' => 'Phone'],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ],
        ]);
    }

    private function rules(?Doctor $doctor = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('doctors', 'email')->ignore($doctor?->id)],
        ];
    }
}
