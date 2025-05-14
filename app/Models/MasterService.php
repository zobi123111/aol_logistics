<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterService extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'origin',
        'destination',
        'service_type',
        'street',
        'city',
        'state',
        'zip',
        'country',
        'service_name',
    ];

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

