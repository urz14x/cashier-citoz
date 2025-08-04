<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Member;
use App\Models\Package;

class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'package_id' => round(1, 2),
            'name' => fake()->name(),
            'social_media' => fake()->word(),
            'phone' => "08288321312",
            'gender' => fake()->randomElement(["M","F"]),
            'joined' => fake()->date(),
            'status' => fake()->randomElement(["active","inactive","expired"]),
        ];
    }
}
