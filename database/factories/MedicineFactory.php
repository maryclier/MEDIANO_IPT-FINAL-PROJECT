<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medicine>
 */
class MedicineFactory extends Factory
{
    protected $model = Medicine::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Paracetamol', 'Ibuprofen', 'Antihistamine', 'Oral rehydration salts']),
            'category' => fake()->randomElement(['Pain relief', 'Allergy', 'First aid', 'General']),
            'quantity' => fake()->numberBetween(0, 200),
            'unit' => fake()->randomElement(['tablets', 'bottles', 'packs']),
            'expiry_date' => fake()->dateTimeBetween('+1 month', '+3 years')->format('Y-m-d'),
            'description' => fake()->sentence(),
        ];
    }
}
