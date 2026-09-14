<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database following the dependency order from agent.md §6.
     */
    public function run(): void
    {
        // 1. 50 Cities
        City::factory(50)->create();

        // 2. 10 Categories
        Category::factory(10)->create();

        // 3. 3 Payment Methods
        PaymentMethod::factory(3)->create();

        // 4. 10 Couriers
        Courier::factory(10)->create();

        // 5. 150 Menus (depends on Categories)
        Menu::factory(150)->create();

        // 6. 200 Customers (depends on Cities)
        Customer::factory(200)->create();

        // 7. 150 Orders with 3-5 OrderItems each (depends on Customers, PaymentMethods, Couriers)
        Order::factory(150)->create()->each(function (Order $order) {
            $itemCount = rand(3, 5);
            for ($i = 0; $i < $itemCount; $i++) {
                $menu = Menu::inRandomOrder()->first();
                $qty = rand(1, 5);
                $order->orderItems()->create([
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'subtotal' => $qty * $menu->price,
                ]);
            }
        });

        // 8. 1 approved admin account for demo/testing
        User::create([
            'name' => 'Admin Rasa Nusantara',
            'email' => 'admin@rasanusantara.test',
            'password' => Hash::make('Admin1234!'),
            'is_approved' => true,
        ]);
    }
}
