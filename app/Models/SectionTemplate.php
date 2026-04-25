<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SectionTemplate extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('preview_image')->singleFile();
    }

    public function getPreviewImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('preview_image') ?: null;
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
