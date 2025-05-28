<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trailerdata extends Model
{
    use HasFactory; 
    protected $fillable = [
        'trailer_num',
        'supplier_id'
    ];
     public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
