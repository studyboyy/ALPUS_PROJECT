<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactFeedback extends Model
{
    protected $table = 'contact_feedback';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
