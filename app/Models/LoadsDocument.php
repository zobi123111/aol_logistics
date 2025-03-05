<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadsDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'load_id',
        'document_type',
        'path',
    ];

    public function loads()
    {
        return $this->belongsTo(Load::class);
    }
}

