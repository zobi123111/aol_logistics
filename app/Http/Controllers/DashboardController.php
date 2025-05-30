<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Load;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request) 
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

        $sessionPath = storage_path('framework/sessions');
        $activeUserIds = [];
        
        foreach (File::files($sessionPath) as $file) {
            $contents = File::get($file->getRealPath());
        
            if (preg_match('/"login_web_[^"]+";i:(\d+)/', $contents, $matches)) {
                $activeUserIds[] = (int) $matches[1];
            }
        }
        
        $activeUsers = User::whereIn('id', array_unique($activeUserIds))->with('roledata')->get();
        $pendingLoads = Load::with('origindata','destinationdata', 'creatorfor', 'assignedServices.supplier' )->where('shipment_status', 'pending')->get();
        if ($request->ajax()) {

            // dd($request->all());
            if ($request->query('type') === 'loads') {
                return DataTables::of($pendingLoads)
                ->addColumn('originval', function ($load) {
                    return $load->origindata
                        ? $load->origindata->street . ', ' . $load->origindata->city . ', ' . $load->origindata->state . ', ' . $load->origindata->country
                        : 'N/A';
                })
                ->addColumn('destinationval', function ($load) {
                    return $load->destinationdata
                        ? $load->destinationdata->street . ', ' . $load->destinationdata->city . ', ' . $load->destinationdata->state . ', ' . $load->destinationdata->country
                        : 'N/A';
                })
                ->addColumn('aol', function ($load) {
                    $deleteId = encode_id($load->id);
                    $showUrl = route('loads.show', $deleteId);
                    return '<a href="' . $showUrl . '" target="_blank" >
                                '.$load->aol_number.'
                            </a>';
                })
                ->addColumn('created_for_user', function ($load) {
                    return optional($load->creatorfor)->business_name ?? optional($load->creatorfor)->email;
                })
                 ->addColumn('supplier_dba', function ($load) {
                    return $load->assignedServices
                        ->pluck('supplier.dba')   // Get all dba names
                        ->filter()                // Remove null values
                        ->unique()                // Avoid duplicates
                        ->implode(', ');         // Join them with a comma
                })
                ->rawColumns(['originval', 'destinationval', 'aol']) 
                    ->addIndexColumn()
                    ->make(true);
            }

                if ($request->query('type') === 'filter') {
                $query = Load::with(['supplierdata', 'creator', 'origindata','destinationdata', 'assignedServices.supplier']);
        
                                if (!empty($request->supplier_ids)) {
                    $query->whereHas('assignedServices', function ($q) use ($request) {
                        $q->whereIn('supplier_id', $request->supplier_ids);
                    });
                }

        
                if (!empty($request->creator_ids)) {
                    $query->whereIn('created_by', $request->creator_ids);
                }
                if (!empty($request->client_ids)) {
                    $query->whereIn('created_for', $request->client_ids);
                }
                if (!empty($request->status_ids)) {
                    $query->whereIn('status', $request->status_ids);
                }     
        
                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('supplier_name', function ($row) {
                        return $row->supplierdata->company_name ?? 'N/A';
                    })
                    ->addColumn('creator_name', function ($row) {
                        return $row->creator->fname ?? 'N/A';
                    })
                    ->addColumn('originval', function ($load) {
                        return $load->origindata
                            ? $load->origindata->street . ', ' . $load->origindata->city . ', ' . $load->origindata->state . ', ' . $load->origindata->country
                            : 'N/A';
                    })
                    ->addColumn('destinationval', function ($load) {
                        return $load->destinationdata
                            ? $load->destinationdata->street . ', ' . $load->destinationdata->city . ', ' . $load->destinationdata->state . ', ' . $load->destinationdata->country
                            : 'N/A';
                    })
                    ->addColumn('supplier_dba', function ($load) {
                            return $load->assignedServices
                                ->map(function ($service) {
                                    return optional($service->supplier)->dba;
                                })
                                ->filter() 
                                ->unique() 
                                ->implode(', ');
                        })
                    ->addColumn('aol', function ($load) {
                        $deleteId = encode_id($load->id);
                        $showUrl = route('loads.show', $deleteId);
                        return '<a href="' . $showUrl . '" target="_blank" >
                                    '.$load->aol_number.'
                                </a>';
                    })
                    ->addColumn('created_for_user', function ($load) {
                        return optional($load->creatorfor)->business_name ?? optional($load->creatorfor)->email;
                    })
                    ->rawColumns(['originval', 'destinationval', 'aol']) 
                    ->make(true);
            }
        }

        // Fetch suppliers and creators for the filters

        //  $query = Load::with(['supplierdata', 'creator']);
        // $suppliers = Supplier::select('id', 'company_name')->get();
        // $creators = User::select('id', 'fname')->get();


        // $suppliers = Load::with('supplierdata')
        // ->selectRaw('DISTINCT supplier_id')
        // ->whereNotNull('supplier_id') 
        // ->get();

        $loads = Load::with('assignedServices.supplier')->get();

$suppliers = $loads
    ->pluck('assignedServices')              // Get all assignedServices per load
    ->flatten()
    ->pluck('supplier')                      // Extract suppliers
    ->filter()                               // Remove nulls
    ->unique('id')                           // Ensure uniqueness by supplier ID
    ->values();

        $creators = Load::with('creator')
        ->selectRaw('DISTINCT created_by')
        ->whereNotNull('created_by') 
        ->get();

        $statuses = Load::selectRaw('DISTINCT status')
                    ->whereNotNull('status')
                    ->get();


        $creatorsclients = Load::with('creatorfor')
        ->selectRaw('DISTINCT created_for')
        ->whereNotNull('created_for') 
        ->get();
            
        return view('Dashboard.index', compact(
            'totalClients', 'activeClients',
            'totalSuppliers', 'activeSuppliers',
            'totalAol', 'activeTotalAol',
            'activeUsers', 'pendingLoads','suppliers', 'creators', 'creatorsclients', 'statuses'
        ));
    }
}
