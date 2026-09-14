<?php

namespace Database\Factories;

use App\Models\Courier;
use App\Models\Customer;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::inRandomOrder()->value('id'),
            'payment_method_id' => PaymentMethod::inRandomOrder()->value('id'),
            'courier_id' => Courier::inRandomOrder()->value('id'),
            'status' => $this->faker->randomElement(['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan']),
        ];
    }
}
