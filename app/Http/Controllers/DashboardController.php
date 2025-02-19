<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() 
    {
        // return view('Dashboard.index');
        // $totalClients = User::where('role', 'client')->count();
        // $activeClients = User::where('role', 'client')->where('is_active', 1)->count();
    
        // $totalSuppliers = User::where('role', 'supplier')->count();
        // $activeSuppliers = User::where('role', 'supplier')->where('is_active', 1)->count();
    
        // $activeAdmins = User::where('role', 'admin')->where('is_active', 1)->count();
        $totalSuppliers = User::where('is_supplier', 1)
        ->count();
        $activeSuppliers = User::where('is_supplier', 1)->where('is_active', 1)
        ->count();

        $totalClients = User::where('is_client', 1)
        ->count();
        $activeClients = User::where('is_client', 1)->where('is_active', 1)
        ->count();  

        $totalAol = User::with('roledata')
        ->whereHas('roledata', function ($query) {
            $query->where('user_type_id', 1);
        })
        ->count();
        $activeTotalAol = User::with('roledata')
        ->whereHas('roledata', function ($query) {
            $query->where('user_type_id', 1);
        })->where('is_active', 1)
        ->count();
// dd($activeClients);
        return view('Dashboard.index', compact(
            'totalClients', 'activeClients',
            'totalSuppliers', 'activeSuppliers',
            'totalAol', 'activeTotalAol'
        ));
    }
}
