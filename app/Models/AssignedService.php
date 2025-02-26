<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedService extends Model
{
    use HasFactory;

    protected $fillable = ['load_id', 'supplier_id', 'service_id'];

    public function loads()
    {
        return $this->belongsTo(Load::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}