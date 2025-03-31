<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Load extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'aol_number', 'origin', 'destination', 'payer', 
        'equipment_type', 'weight', 'delivery_deadline', 
        'customer_po', 'is_hazmat', 'is_inbond', 'status', 'service_type', 'supplier_id', 'trailer_number', 'port_of_entry', 'created_by', 'schedule', 'truck_number', 'driver_name', 'driver_contact_no', 'shipment_status', 'weight_unit', 'created_for'
    ];

    protected $casts = [
        'is_hazmat' => 'boolean',
        'is_inbond' => 'boolean',
        'delivery_deadline' => 'date',
        'schedule' => 'datetime', 
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($load) {
            $load->aol_number = self::generateUniqueAOL();
        });
    }

    public static function generateUniqueAOL()
    {
        $lastAOL = self::withTrashed()->max('aol_number');
        $newAOL = is_numeric($lastAOL) ? intval($lastAOL) + 1 : 1001;
        return (string) $newAOL;
    }
    
    // Relationship with Origin (Address)
    public function origindata()
    {
        return $this->belongsTo(Origin::class, 'origin');
    }

    // Relationship with Destination (Address)
    public function destinationdata()
    {
        return $this->belongsTo(Destination::class, 'destination');
    }

    // Relationship with Destination (Address)
    public function supplierdata()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function assignedServices()
    {
        return $this->hasMany(AssignedService::class, 'load_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function creatorfor() {
        return $this->belongsTo(User::class, 'created_for');
    }

    public function documents()
{
    return $this->hasMany(LoadsDocument::class);
}
public function invoice()
{
    return $this->belongsTo(Invoice::class);
}
public function invoices()
{
    return $this->hasMany(Invoice::class, 'load_id', 'id');
}
}
