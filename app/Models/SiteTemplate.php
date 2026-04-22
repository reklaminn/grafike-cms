<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme_id',
        'name',
        'slug',
        'description',
        'snapshot_json',
        'preview_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_json' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
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
