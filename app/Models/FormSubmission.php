<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'user_id', 'reference_id', 'subject', 'data',
        'reply', 'status', 'ip_address', 'user_agent', 'recipient_email',
    ];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'new');
    }
}
