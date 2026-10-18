<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'medicine_id' => Medicine::factory(),
            'diagnosis' => fake()->randomElement(['Seasonal allergies', 'Common cold', 'Minor sprain', 'Routine checkup']),
            'treatment' => fake()->sentence(),
            'dosage' => fake()->randomElement(['1 tablet twice daily', 'As needed', 'Follow label instructions']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
