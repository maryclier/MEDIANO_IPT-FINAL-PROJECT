<?php 
namespace App\Http\Controllers; 
use App\Models\Patient;
 use Illuminate\Http\Request; 
 class PatientController extends Controller
  {
     public function index() 
    { 
        $patients = Patient::all(); 
        return view('patients.index', compact('patients')); 
    } 

    public function create() 
    { 
        return view('patients.create'); 
    } 

    public function store(Request $request) 
    { 
        $request->validate([ 
            'name' => 'required|string|max:255', 
            'age' => 'nullable|integer|min:0', 
            'gender' => 'nullable|string|max:20', 
            'address' => 'nullable|string', 
            'phone' => 'nullable|string|max:20', 
            'email' => 'nullable|email|max:255', 
        ]); 

        Patient::create($request->only([ 
            'name', 
            'age', 
            'gender', 
            'address', 
            'phone', 
            'email' 
        ])); 

        return redirect()->route('patients.index') 
            ->with('success', 'Patient added successfully!'); 
    } 

    public function edit(Patient $patient) 
    { 
        return view('patients.edit', compact('patient')); 
    } 

    public function update(Request $request, Patient $patient) 
    { 
        $request->validate([ 
            'name' => 'required|string|max:255', 
            'age' => 'nullable|integer|min:0', 
            'gender' => 'nullable|string|max:20', 
            'address' => 'nullable|string', 
            'phone' => 'nullable|string|max:20', 
            'email' => 'nullable|email|max:255', 
        ]); 

        $patient->update($request->only([ 
            'name', 
            'age', 
            'gender', 
            'address', 
            'phone', 
            'email' 
        ])); 

        return redirect()->route('patients.index') 
            ->with('success', 'Patient updated successfully!'); 
    } 

    public function destroy(Patient $patient) 
    { 
        $patient->delete(); 
        return redirect()->route('patients.index') 
            ->with('success', 'Patient deleted successfully!'); 
    } 
}