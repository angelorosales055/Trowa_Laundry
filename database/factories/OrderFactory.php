<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'customer_id' => Customer::factory(),
            'customer_name' => fake()->name(),
            'weight_kg' => fake()->randomFloat(2, 1, 10),
            'services' => 'Wash & Fold',
            'total_price' => 250,
            'status' => 'pending',
        ];
    }
}
