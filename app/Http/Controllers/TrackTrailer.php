<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Trailerdata;

class TrackTrailer extends Controller
{
    public function lastposition(){
         $trailers = Trailerdata::all();
        return view('tracktrailer.lastpostion', compact('trailers'));
    }
}
