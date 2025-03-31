<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [  'load_id', 'external_invoice_id', 'status'];

    public function loads()
    {
        return $this->hasOne(Load::class);
    }
}
