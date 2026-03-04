<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraphs(3, true),
            'excerpt' => fake()->sentence(10),
            'page_id' => Page::factory(),
            'language_id' => Language::factory(),
            'status' => 'published',
            'sort_order' => fake()->numberBetween(0, 100),
            'slug' => fake()->unique()->slug(4),
            'is_featured' => false,
            'meta_description' => fake()->sentence(8),
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
