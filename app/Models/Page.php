<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Page extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasRecursiveRelationships;

    protected $fillable = [
        'title', 'parent_id', 'language_id', 'root_page_id', 'status',
        'show_in_menu', 'sort_order', 'slug', 'external_url', 'link_target',
        'module_type', 'template', 'layout_json', 'page_template',
        'is_password_protected', 'page_password', 'show_social_share',
        'show_facebook_comments', 'show_breadcrumb', 'view_count', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'layout_json' => 'array',
            'is_password_protected' => 'boolean',
            'show_in_menu' => 'boolean',
            'show_social_share' => 'boolean',
            'show_facebook_comments' => 'boolean',
            'show_breadcrumb' => 'boolean',
        ];
    }

    public function getParentKeyName(): string
    {
        return 'parent_id';
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort_order');
    }

    public function articles()
    {
        return $this->hasMany(Article::class)->orderBy('sort_order');
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function seo()
    {
        return $this->morphOne(SeoEntry::class, 'seoable');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByLanguage($query, $languageId)
    {
        return $query->where('language_id', $languageId);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
    }
}
