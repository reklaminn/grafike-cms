<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'engine',
        'description',
        'assets_json',
        'tokens_json',
        'settings_schema_json',
        'preview_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'assets_json' => 'array',
            'tokens_json' => 'array',
            'settings_schema_json' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function sectionTemplates()
    {
        return $this->hasMany(SectionTemplate::class);
    }

    public function pageTemplates()
    {
        return $this->hasMany(PageTemplate::class);
    }

    public function siteTemplates()
    {
        return $this->hasMany(SiteTemplate::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
