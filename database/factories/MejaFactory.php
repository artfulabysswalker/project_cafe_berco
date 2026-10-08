<?php

namespace Database\Factories;

use App\Models\Meja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meja>
 */
class MejaFactory extends Factory
{
    protected $model = Meja::class;

    public function definition(): array
    {
        return [
            'nama_meja' => 'Meja '.fake()->unique()->numberBetween(1, 999),
            'kapasitas' => fake()->numberBetween(2, 8),
            'status' => 'tersedia',
            'area' => fake()->randomElement(['Indoor', 'Outdoor', 'VIP']),
        ];
    }
}
