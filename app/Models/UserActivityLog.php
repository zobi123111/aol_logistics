<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;
    const LOG_TYPE_CREATE_USER = 'create_user';
    const LOG_TYPE_EDIT_USER = 'edit_user';
    const LOG_TYPE_DELETE_USER = 'delete_user';
    const LOG_TYPE_UPDATE_STATUS = 'change_status_user'; 
    const LOG_TYPE_CREATE_ROLE = 'create_role';
    const LOG_TYPE_EDIT_ROLE = 'edit_role';
    const LOG_TYPE_DELETE_ROLE = 'delete_role';
    protected $fillable = ['log_type', 'description', 'user_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
