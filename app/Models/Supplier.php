<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import the SoftDeletes trait

class Supplier extends Model
{
    use HasFactory, SoftDeletes; // Use the SoftDeletes trait

    protected $fillable = [
        'company_name', 'dba', 'street_address', 'city', 'state', 'zip_code', 'country',
        'office_phone', 'primary_contact_email', 'primary_contact_office_phone', 
        'primary_contact_mobile_phone', 'user_email', 'user_office_phone', 'user_mobile_phone',
        'user_role', 'service_type', 'currency', 'preferred_language',
        'documents', 'scac_number', 'scac_documents', 'caat_number', 'caat_documents', 'user_id'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function units()
    {
        return $this->hasMany(SupplierUnit::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
