<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'id',
        'role_name',
        'user_type_id'
    ];

    protected $dates = ['deleted_at'];

     // Relationship to UserType
     public function userType()
     {
         return $this->belongsTo(UserType::class);
     }

     public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function users()
{
    return $this->hasMany(User::class, 'role');
}
}