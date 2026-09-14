<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $menu = Menu::inRandomOrder()->first();
        $qty = $this->faker->numberBetween(1, 5);

        return [
            'order_id' => Order::inRandomOrder()->value('id'),
            'menu_id' => $menu->id,
            'qty' => $qty,
            'subtotal' => $qty * $menu->price,
        ];
    }
}
