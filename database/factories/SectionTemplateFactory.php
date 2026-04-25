<?php

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'theme_id'             => Theme::factory(),
            'type'                 => $this->faker->randomElement(['hero', 'features', 'cta', 'rich-text', 'cards']),
            'variation'            => 'default',
            'name'                 => $this->faker->words(3, true),
            'render_mode'          => 'html',
            'component_key'        => null,
            'legacy_module_key'    => null,
            'html_template'        => '<div>{{title}}</div>',
            'schema_json'          => [],
            'legacy_config_map_json' => null,
            'default_content_json' => [],
            'is_active'            => true,
        ];
    }
}
