<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignAsset extends Model
{
    protected $fillable = [
        'type', 'name', 'content', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
