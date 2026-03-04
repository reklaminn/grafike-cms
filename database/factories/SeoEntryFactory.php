<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Page;
use App\Models\SeoEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeoEntryFactory extends Factory
{
    protected $model = SeoEntry::class;

    public function definition(): array
    {
        return [
            'seoable_id' => Page::factory(),
            'seoable_type' => Page::class,
            'slug' => fake()->unique()->slug(3),
            'language_id' => Language::factory(),
            'meta_title' => fake()->sentence(5),
            'meta_description' => fake()->sentence(10),
            'meta_keywords' => implode(', ', fake()->words(5)),
            'is_noindex' => false,
        ];
    }

    public function forPage(Page $page): static
    {
        return $this->state(fn () => [
            'seoable_id' => $page->id,
            'seoable_type' => Page::class,
        ]);
    }

    public function noindex(): static
    {
        return $this->state(fn () => ['is_noindex' => true]);
    }
}
