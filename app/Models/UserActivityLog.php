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

    const LOG_TYPE_UPDATE_STATUS_SUPPLIER = 'change_status_supplier'; 
    const LOG_TYPE_CREATE_SUPPLIER = 'create_supplier';
    const LOG_TYPE_EDIT_SUPPLIER = 'edit_supplier';
    const LOG_TYPE_DELETE_SUPPLIER = 'delete_supplier';

    const LOG_TYPE_UPDATE_STATUS_CLIENT = 'change_status_client'; 
    const LOG_TYPE_CREATE_CLIENT = 'create_client';
    const LOG_TYPE_EDIT_CLIENT = 'edit_client';
    const LOG_TYPE_DELETE_CLIENT = 'delete_client';


    const LOG_TYPE_ADD_LOAD = 'add_load';
    const LOG_TYPE_ASSIGN_LOAD = 'assign_load';
    const LOG_TYPE_UNASSIGN_LOAD = 'unassign_load';
    const LOG_TYPE_LOAD_STATUS_CHANGE = 'load_status_change';

    
    protected $fillable = ['log_type', 'description', 'user_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
