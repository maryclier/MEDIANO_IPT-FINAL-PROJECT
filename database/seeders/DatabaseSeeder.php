<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = Doctor::factory(3)->create();
        $patients = Patient::factory(20)->create();
        $medicines = Medicine::factory(10)->create();
        $appointments = collect();

        for ($index = 0; $index < 25; $index++) {
            $appointments->push(Appointment::factory()->create([
                'doctor_id' => $doctors->random()->id,
                'patient_id' => $patients->random()->id,
            ]));
        }

        $appointments->take(12)->each(function (Appointment $appointment) use ($medicines): void {
            $appointment->update(['status' => 'completed']);
            MedicalRecord::factory()->create([
                'appointment_id' => $appointment->id,
                'medicine_id' => $medicines->random()->id,
            ]);
        });
    }
}
