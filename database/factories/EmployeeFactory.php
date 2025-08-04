<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        return [
            'name' => $name,
            'social_media' => '@'.str($this->faker->name())->slug(),
            'phone' => $this->faker->phoneNumber(),
            'position' => $this->faker->jobTitle(),
            'birth_date' => $this->faker->date(),
           'photo' => 'https://ui-avatars.com/api/?name=' . urlencode($name),
        ];
    }
}
