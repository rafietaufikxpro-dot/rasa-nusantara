<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    private static array $menuNames = [
        'Rendang Daging Sapi', 'Ayam Pop Padang', 'Nasi Goreng Spesial', 'Soto Ayam Lamongan',
        'Gado-Gado Jakarta', 'Mie Goreng Jawa', 'Opor Ayam', 'Gulai Ikan Kakap',
        'Capcay Kuah', 'Tumis Kangkung', 'Rawon Surabaya', 'Bakso Malang',
        'Sate Kambing', 'Nasi Uduk Betawi', 'Pecel Lele', 'Ayam Bakar Kecap',
        'Sup Buntut', 'Ikan Asin Balado', 'Tempe Orek', 'Sayur Lodeh',
        'Semur Daging', 'Coto Makassar', 'Pallubasa', 'Konro Bakar',
        'Pempek Palembang', 'Tekwan', 'Nasi Kapau', 'Dendeng Balado',
        'Bebek Goreng', 'Ayam Penyet', 'Lontong Sayur', 'Ketoprak',
        'Bubur Ayam', 'Nasi Liwet', 'Tahu Gimbal', 'Kupat Tahu',
        'Es Teh Manis', 'Es Jeruk', 'Jus Alpukat', 'Jus Mangga',
        'Teh Tarik', 'Kopi Susu', 'Es Cincau', 'Es Campur',
        'Pisang Goreng', 'Klepon', 'Dadar Gulung', 'Onde-Onde',
        'Serabi', 'Kue Lumpur', 'Martabak Manis', 'Bika Ambon',
        'Kolak Pisang', 'Cendol', 'Es Dawet', 'Putu Ayu',
        'Lumpia Semarang', 'Siomay Bandung', 'Batagor', 'Cireng',
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $name = self::$menuNames[self::$index % count(self::$menuNames)];
        self::$index++;

        return [
            'name' => $name,
            'price' => $this->faker->numberBetween(15000, 85000),
            'category_id' => Category::inRandomOrder()->value('id'),
        ];
    }
}
