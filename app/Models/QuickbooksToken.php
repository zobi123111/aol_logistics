<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class QuickbooksToken extends Model
{
    use HasFactory;
      protected $fillable = [
        'access_token', 'refresh_token', 'access_token_expires_at',
    ];
}
