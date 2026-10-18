<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'appointment_date' => fake()->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
            'appointment_time' => fake()->time('H:i'),
            'status' => fake()->randomElement(['scheduled', 'completed', 'cancelled']),
            'reason' => fake()->randomElement(['Headache', 'Annual checkup', 'Minor injury', 'Stomach ache']),
        ];
    }
}
