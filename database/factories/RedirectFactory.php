<?php

namespace Database\Factories;

use App\Models\Redirect;
use Illuminate\Database\Eloquent\Factories\Factory;

class RedirectFactory extends Factory
{
    protected $model = Redirect::class;

    public function definition(): array
    {
        return [
            'from_url' => '/' . fake()->unique()->slug(2),
            'to_url' => '/' . fake()->slug(2),
            'status_code' => 301,
            'is_active' => true,
            'hit_count' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function temporary(): static
    {
        return $this->state(fn () => ['status_code' => 302]);
    }
}
