<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reviewable_id', 'reviewable_type', 'author_name', 'author_email',
        'rating', 'title', 'body', 'is_approved', 'ip_address', 'language_id', 'legacy_id',
    ];

    protected function casts(): array
    {
        return ['is_approved' => 'boolean'];
    }

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
