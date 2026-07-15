<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasProdiScope;

class ContactFeedback extends Model
{
    use HasProdiScope;
    protected $table = 'contact_feedback';

    protected $fillable = [
        'name',
        'prodi_id',
        'email',
        'subject',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
