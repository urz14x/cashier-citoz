<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Member;
use App\Models\Order;
use App\Models\User;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'member_id' => Member::factory(),
            'order_number' => fake()->word(),
            'order_name' => fake()->word(),
            'discount' => fake()->numberBetween(-10000, 10000),
            'total' => fake()->numberBetween(-10000, 10000),
            'profit' => fake()->numberBetween(-10000, 10000),
            'payment_method' => fake()->word(),
            'status' => fake()->word(),
        ];
    }
}
