<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_id',
        'type',
        'variation',
        'name',
        'render_mode',
        'component_key',
        'legacy_module_key',
        'html_template',
        'schema_json',
        'legacy_config_map_json',
        'default_content_json',
        'preview_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'schema_json' => 'array',
            'legacy_config_map_json' => 'array',
            'default_content_json' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
