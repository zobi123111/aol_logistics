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
        'customer_po', 'is_hazmat', 'is_inbond', 'status'
    ];

    protected $casts = [
        'is_hazmat' => 'boolean',
        'is_inbond' => 'boolean',
        'delivery_deadline' => 'date',
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
        do {
            $numbers = substr(str_shuffle('0123456789'), 0, 6); 
            $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
            
            $aol = str_shuffle($numbers . $letters);
        } while (self::where('aol_number', $aol)->exists());
    
        return $aol;
    }
    
    
}
