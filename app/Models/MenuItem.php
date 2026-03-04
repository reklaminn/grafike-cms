<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class MenuItem extends Model
{
    use HasFactory, HasRecursiveRelationships;

    protected $fillable = [
        'menu_id', 'parent_id', 'title', 'url', 'page_id', 'target',
        'css_class', 'icon', 'sort_order', 'is_active', 'custom_html',
        'json_config', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'json_config' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getParentKeyName(): string
    {
        return 'parent_id';
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
