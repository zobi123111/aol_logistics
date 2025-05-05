<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCost extends Model
{
    use HasFactory;

    protected $table = 'client_cost'; // explicitly set the table name

    protected $fillable = [
        'client_id',
        'service_id',
        'supplier_id',
        'client_cost',
    ];

    /**
     * Relationship: belongs to a client (user)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Relationship: belongs to a service
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Relationship: belongs to a supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
