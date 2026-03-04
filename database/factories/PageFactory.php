<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(3),
            'parent_id' => null,
            'language_id' => Language::factory(),
            'status' => 'published',
            'show_in_menu' => true,
            'sort_order' => fake()->numberBetween(0, 100),
            'module_type' => 0,
            'layout_json' => [
                'header' => [],
                'body' => [
                    [
                        'columns' => [
                            ['width' => 12, 'modules' => [['type' => 90, 'config' => []]]]
                        ]
                    ]
                ],
                'footer' => [],
            ],
            'is_password_protected' => false,
            'show_social_share' => false,
            'show_breadcrumb' => true,
            'view_count' => 0,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => 'archived']);
    }

    public function passwordProtected(): static
    {
        return $this->state(fn () => [
            'is_password_protected' => true,
            'page_password' => bcrypt('secret123'),
        ]);
    }

    public function withParent(Page $parent): static
    {
        return $this->state(fn () => ['parent_id' => $parent->id]);
    }
}
