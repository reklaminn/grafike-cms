<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'key', 'value', 'group', 'type'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public static function get(string $key, $default = null, ?int $siteId = null): mixed
    {
        $cacheKey = "setting_{$siteId}_{$key}";

        return Cache::remember($cacheKey, 600, function () use ($key, $default, $siteId) {
            if ($siteId) {
                $siteSetting = static::where('key', $key)->where('site_id', $siteId)->first();
                if ($siteSetting) {
                    return $siteSetting->value;
                }
            }

            $setting = static::where('key', $key)->whereNull('site_id')->first();

            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value, string $group = 'general', ?int $siteId = null): void
    {
        static::updateOrCreate(
            ['key' => $key, 'site_id' => $siteId],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting_{$siteId}_{$key}");
    }
}
