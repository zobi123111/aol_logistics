<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class SupplierDocument extends Model
{
    use HasFactory;
    use SoftDeletes;
     protected $fillable = ['supplier_id', 'document_type', 'file_path'];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
