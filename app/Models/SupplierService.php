<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierService extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'master_service_id',
        'cost',
        'service_date',
        'supplier_id',
        'schedule_cost'
    ];
    protected $casts = [
        'service_date' => 'date', // This is important
    ];
    public function masterService()
    {
        return $this->belongsTo(MasterService::class);
    }


    public function clientServices()
    {
        return $this->hasMany(ClientService::class, 'master_service_id', 'master_service_id');
    }
}

