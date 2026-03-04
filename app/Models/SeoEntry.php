<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'seoable_id', 'seoable_type', 'slug', 'language_id', 'meta_title',
        'meta_description', 'meta_keywords', 'h1_override', 'canonical_url',
        'hreflang_tags', 'is_noindex', 'page_css', 'page_js', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'is_noindex' => 'boolean',
            'hreflang_tags' => 'array',
        ];
    }

    public function seoable()
    {
        return $this->morphTo();
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
