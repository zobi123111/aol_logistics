<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Trailerdata;
use App\Models\SupplierUnit;

class TrackTrailer extends Controller
{
    public function lastposition(){
        $trailers = Trailerdata::orderBy('trailer_num', 'asc')->get();
               $assignedSuppliers = SupplierUnit::with('supplier')
        ->get()
        ->unique('supplier_id')
        ->values(); 


        return view('tracktrailer.lastpostion', compact('trailers', 'assignedSuppliers'));
    }
}
