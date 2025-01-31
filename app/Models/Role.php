<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'role_name',
        'user_type_id'
    ];

     // Relationship to UserType
     public function userType()
     {
         return $this->belongsTo(userType::class);
     }

     public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }
}
