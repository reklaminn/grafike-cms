<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpProfile extends Model
{
    protected $fillable = [
        'name', 'host', 'port', 'encryption', 'username', 'password',
        'from_email', 'from_name', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'password' => 'encrypted',
        ];
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
