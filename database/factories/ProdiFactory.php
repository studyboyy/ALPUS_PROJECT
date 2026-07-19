<?php

namespace Database\Factories;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prodi>
 */
class ProdiFactory extends Factory
{
    protected $model = Prodi::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'code' => strtoupper($this->faker->unique()->bothify('PRD##')),
            'name' => ucwords($name),
            'is_active' => true,
        ];
    }

    public function sistemInformasi(): static
    {
        return $this->state([
            'code' => 'SI',
            'name' => 'Sistem Informasi',
            'is_active' => true,
        ]);
    }

    public function informatika(): static
    {
        return $this->state([
            'code' => 'IF',
            'name' => 'Informatika',
            'is_active' => true,
        ]);
    }

    public function ekonomi(): static
    {
        return $this->state([
            'code' => 'EKONOMI',
            'name' => 'Ekonomi',
            'is_active' => true,
        ]);
    }

    public function manajemen(): static
    {
        return $this->state([
            'code' => 'MJ',
            'name' => 'Manajemen',
            'is_active' => true,
        ]);
    }
}
