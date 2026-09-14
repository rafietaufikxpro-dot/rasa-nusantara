<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    private static array $methods = ['Transfer Bank', 'E-Wallet', 'COD'];

    private static int $index = 0;

    public function definition(): array
    {
        $method = self::$methods[self::$index % count(self::$methods)];
        self::$index++;

        return [
            'name' => $method,
        ];
    }
}
