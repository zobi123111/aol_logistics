<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Trailerdata;

class TrackTrailer extends Controller
{
    public function lastposition(){
        $trailers = Trailerdata::orderBy('trailer_num', 'asc')->get();
               $assignedSuppliers = Trailerdata::with('supplier')
        ->get()
        ->unique('supplier_id')
        ->values(); 


        return view('tracktrailer.lastpostion', compact('trailers', 'assignedSuppliers'));
    }
}
