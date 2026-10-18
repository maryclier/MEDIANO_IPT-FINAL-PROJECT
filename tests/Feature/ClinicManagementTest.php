<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_and_all_clinic_modules_load(): void
    {
        $this->get('/')->assertOk();
        $this->get('/doctors')->assertOk();
        $this->get('/patients')->assertOk();
        $this->get('/appointments')->assertOk();
        $this->get('/medical-records')->assertOk();
        $this->get('/medicines')->assertOk();
    }

    public function test_patient_creation_and_validation(): void
    {
        $this->post('/patients', [
            'name' => 'Jamie Student',
            'age' => 12,
            'gender' => 'Female',
            'email' => 'jamie@example.test',
        ])->assertRedirect('/patients');

        $this->assertDatabaseHas('patients', [
            'name' => 'Jamie Student',
            'age' => 12,
        ]);

        $this->from('/patients/create')->post('/patients', [
            'name' => 'Invalid Student',
            'age' => -1,
        ])->assertSessionHasErrors('age');
    }

    public function test_appointment_and_medical_record_are_related(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        $medicine = Medicine::factory()->create();

        $this->post('/appointments', [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'appointment_date' => today()->toDateString(),
            'appointment_time' => '09:00',
            'status' => 'completed',
            'reason' => 'Routine checkup',
        ])->assertRedirect('/appointments');

        $appointment = Appointment::firstOrFail();

        $this->post('/medical-records', [
            'appointment_id' => $appointment->id,
            'medicine_id' => $medicine->id,
            'diagnosis' => 'Seasonal allergies',
            'treatment' => 'Rest and fluids',
            'dosage' => 'One tablet daily',
        ])->assertRedirect('/medical-records');

        $this->assertDatabaseHas('medical_records', [
            'appointment_id' => $appointment->id,
            'medicine_id' => $medicine->id,
            'diagnosis' => 'Seasonal allergies',
        ]);
        $this->get('/appointments/'.$appointment->id)->assertOk()->assertSee($patient->name)->assertSee($doctor->name);
    }

    public function test_related_history_blocks_patient_and_doctor_deletion(): void
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        Appointment::factory()->create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
        ]);

        $this->from('/doctors')->delete('/doctors/'.$doctor->id)->assertSessionHas('error');
        $this->from('/patients')->delete('/patients/'.$patient->id)->assertSessionHas('error');
        $this->assertDatabaseHas('doctors', ['id' => $doctor->id]);
        $this->assertDatabaseHas('patients', ['id' => $patient->id]);
    }

    public function test_medicine_deletion_keeps_medical_record(): void
    {
        $medicine = Medicine::factory()->create();
        $appointment = Appointment::factory()->create();
        $record = MedicalRecord::factory()->create([
            'appointment_id' => $appointment->id,
            'medicine_id' => $medicine->id,
        ]);

        $this->delete('/medicines/'.$medicine->id)->assertRedirect('/medicines');

        $this->assertDatabaseHas('medical_records', [
            'id' => $record->id,
            'medicine_id' => null,
        ]);
    }
}
