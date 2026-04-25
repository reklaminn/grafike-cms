<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ThemeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name'                => $name,
            'slug'                => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'engine'              => 'blade',
            'description'         => null,
            'assets_json'         => null,
            'tokens_json'         => null,
            'settings_schema_json' => null,
            'preview_image'       => null,
            'is_active'           => true,
        ];
    }
}
