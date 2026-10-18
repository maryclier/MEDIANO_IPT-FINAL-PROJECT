<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::withCount('medicalRecords')->orderBy('name')->paginate(15);

        return view('resources.index', [
            'title' => 'Medicines',
            'resourceName' => 'medicines',
            'records' => $medicines,
            'columns' => [
                ['label' => 'Name', 'key' => 'name'],
                ['label' => 'Category', 'key' => 'category'],
                ['label' => 'Stock', 'key' => 'quantity'],
                ['label' => 'Unit', 'key' => 'unit'],
                ['label' => 'Expiry date', 'key' => 'expiry_date'],
            ],
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        Medicine::create($request->validate($this->rules()));

        return redirect()->route('medicines.index')->with('success', 'Medicine added successfully.');
    }

    public function show(Medicine $medicine)
    {
        $medicine->loadCount('medicalRecords');

        return view('resources.show', [
            'title' => 'Medicine details',
            'resourceName' => 'medicines',
            'resource' => $medicine,
            'details' => [
                'Name' => $medicine->name,
                'Category' => $medicine->category,
                'Quantity in stock' => $medicine->quantity.' '.$medicine->unit,
                'Expiry date' => $medicine->expiry_date?->format('M j, Y'),
                'Description' => $medicine->description,
                'Medical records' => $medicine->medical_records_count,
            ],
        ]);
    }

    public function edit(Medicine $medicine)
    {
        return $this->form($medicine);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $medicine->update($request->validate($this->rules()));

        return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully.');
    }

    private function form(?Medicine $medicine = null)
    {
        return view('resources.form', [
            'title' => $medicine ? 'Edit medicine' : 'Add medicine',
            'resourceName' => 'medicines',
            'resource' => $medicine,
            'fields' => [
                ['name' => 'name', 'label' => 'Medicine name', 'required' => true],
                ['name' => 'category', 'label' => 'Category'],
                ['name' => 'quantity', 'label' => 'Quantity in stock', 'type' => 'number', 'required' => true, 'attributes' => ['min' => 0]],
                ['name' => 'unit', 'label' => 'Unit (e.g. tablets, bottles)'],
                ['name' => 'expiry_date', 'label' => 'Expiry date', 'type' => 'date', 'value' => $medicine?->getRawOriginal('expiry_date')],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
            ],
        ]);
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'expiry_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
