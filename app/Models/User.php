<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'fname',
        'lname',
        'name',
        'role',
        'email',
        'password',
        'otp',
        'otp_expires_at',
        'created_by',
        'profile_photo',
        'last_login_at',
        'is_client',
        'supplier_id',
        'is_supplier',
        'client_id',
        'business_name',
        'mobile_number', 
        'country_code', 
        'dba'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roledata()
{
    return $this->belongsTo(Role::class, 'role');
}

public function supplier()
{
    return $this->hasOne(Supplier::class, 'user_id');
}

public function client(): BelongsTo
{
    return $this->belongsTo(User::class, 'client_id');
}


public function clients(): HasMany
{
    return $this->hasMany(User::class, 'client_id');
}
}