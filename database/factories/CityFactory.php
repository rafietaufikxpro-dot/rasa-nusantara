<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Indonesian city names pool.
     *
     * @var array<string>
     */
    private static array $cities = [
        'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara',
        'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Tangerang',
        'Depok', 'Bekasi', 'Bogor', 'Pekanbaru', 'Bandar Lampung', 'Padang', 'Malang',
        'Denpasar', 'Samarinda', 'Batam', 'Balikpapan', 'Banjarmasin', 'Pontianak',
        'Yogyakarta', 'Surakarta', 'Manado', 'Ambon', 'Jayapura', 'Kupang', 'Mataram',
        'Bengkulu', 'Palu', 'Kendari', 'Gorontalo', 'Ternate', 'Sorong', 'Tarakan',
        'Cirebon', 'Tasikmalaya', 'Sukabumi', 'Garut', 'Cilegon', 'Serang', 'Karawang',
        'Purwokerto', 'Tegal', 'Pekalongan', 'Magelang',
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $city = self::$cities[self::$index % count(self::$cities)];
        self::$index++;

        return [
            'name' => $city,
        ];
    }
}
