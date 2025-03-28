<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes; // Use the SoftDeletes trait

    protected $fillable = [
        'origin',
        'destination',
        'cost',
        'supplier_id',
        'service_type','street', 'city', 'state', 'zip', 'country', 'service_name', 
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
}
