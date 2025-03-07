<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AssignedService extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = ['load_id', 'supplier_id', 'service_id', 'cancel_reason', 'quantity'];

    protected $dates = ['deleted_at'];

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