<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientService extends Model
{
    use HasFactory;

    protected $table = 'client_services'; 

    protected $fillable = [
        'client_id',
        'master_service_id',
        'cost',
        'service_date',
        'schedule_cost',
    ];

    // Dates cast
    protected $casts = [
        'service_date' => 'date',
    ];

    // Relationship with User (client)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with MasterService
    public function masterService()
    {
        return $this->belongsTo(MasterService::class, 'master_service_id');
    }
}
