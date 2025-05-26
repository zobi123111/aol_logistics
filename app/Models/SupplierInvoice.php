<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $table = 'supplier_invoices';

    protected $fillable = [
        'load_id',
        'supplier_id',
        'file_path',
        'quickbook_invoice_id',
        'status',
        'bill_no'
    ];

    /**
     * Get the load associated with this supplier invoice.
     */
    public function loads()
    {
        return $this->belongsTo(Load::class);
    }

    /**
     * Get the supplier who created this invoice.
     */
    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
}
