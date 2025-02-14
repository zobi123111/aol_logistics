<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'unit_type',
        'unit_number',
        'license_plate',
        'state',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
