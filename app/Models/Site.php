<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'theme_id',
        'site_template_id',
        'tokens_json',
        'settings_json',
        'custom_css',
        'custom_js',
        'status',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'tokens_json' => 'array',
            'settings_json' => 'array',
            'is_primary' => 'boolean',
        ];
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function siteTemplate()
    {
        return $this->belongsTo(SiteTemplate::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function settings()
    {
        return $this->hasMany(SiteSetting::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function resolve(?string $host = null): ?self
    {
        $host = $host ?: request()->header('X-Site-Host') ?: request()->getHost();

        return static::query()
            ->with('theme')
            ->when($host, fn ($query) => $query->where('domain', $host))
            ->active()
            ->first()
            ?: static::query()->with('theme')->active()->where('is_primary', true)->first()
            ?: static::query()->with('theme')->active()->first();
    }
}
