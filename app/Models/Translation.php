<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['language_id', 'group', 'key', 'value'];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
