<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        $code = fake()->unique()->lexify('??');

        return [
            'name' => fake()->word() . ' Language',
            'code' => $code,
            'locale' => $code . '_' . strtoupper($code),
            'is_active' => true,
            'direction' => 'ltr',
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
