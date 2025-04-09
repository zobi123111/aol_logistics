<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailJob extends Model
{
    protected $table = 'email_jobs'; // optional if table name matches Laravel's convention

    protected $fillable = [
        'to_email',
        'subject',
        'template',
        'payload',
        'email_type',
        'status',
    ];

    protected $casts = [
        'to_email' => 'array',
        'payload' => 'array',
    ];
}
