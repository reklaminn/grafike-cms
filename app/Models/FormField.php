<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'label', 'name', 'type', 'placeholder', 'default_value',
        'options', 'validation_rules', 'is_required', 'sort_order', 'css_class', 'section',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
        ];
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
