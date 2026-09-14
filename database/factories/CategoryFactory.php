<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    private static array $categories = [
        'Makanan Utama', 'Minuman', 'Snack & Camilan', 'Dessert', 'Sayuran',
        'Lauk Pauk', 'Nasi & Bubur', 'Sup & Soto', 'Seafood', 'Vegetarian',
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $cat = self::$categories[self::$index % count(self::$categories)];
        self::$index++;

        return [
            'name' => $cat,
        ];
    }
}
