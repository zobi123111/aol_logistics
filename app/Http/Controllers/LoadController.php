<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Load;
use App\Models\Supplier;
use Yajra\DataTables\DataTables;
use App\Models\Origin;
use App\Models\Destination;
use App\Models\AssignedService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $loads = Load::get();
        // return view('loads.index', compact('loads'));
        $user = auth()->user();
        $userType = $user->roledata->user_type_id;
        if ($request->ajax()) {
            // $loads = Load::with(['origindata', 'destinationdata', 'supplierdata',  'assignedServices.supplier', 'creator'])->latest('id')->get(); 
            $loads = Load::with(['origindata', 'destinationdata', 'supplierdata', 'assignedServices.supplier', 'creator'])
            ->when($userType == 2, function ($query) use ($user) {
                return $query->where('created_by', $user->id)
                ->where(function ($q) {
                    $q->whereNull('schedule')
                      ->orWhereDate('schedule', '<=', now());
                });
            
            })
            ->when($userType == 3, function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->whereHas('assignedServices', function ($q) use ($user) {
                        $q->whereHas('supplier', function ($q) use ($user) {
                            $q->where('id', $user->supplier->id);
                        });
                    })->orWhere('created_by', $user->id);
                })   ->where(function ($q) {
                    $q->whereNull('schedule')
                      ->orWhereDate('schedule', '<=', now());
                });;
            })
            
            ->latest('id')
            ->get();

            return DataTables::of($loads)
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
            ->addColumn('suppliercompany', function ($load) {
                return $load->supplierdata ? $load->supplierdata->company_name: 'N/A';
                    
            })
            ->addColumn('supplier_company_name', function ($load) {
                if ($load->assignedServices->isNotEmpty()) {
                    return $load->assignedServices->pluck('supplier.company_name')->filter()->join(', ');
                }
                return '---';
            })
            ->addColumn('created_by', function ($load) {
                return $load->creator ? $load->creator->fname.' '. $load->creator->lname  : 'N/A';
            })
            ->addColumn('actions', function ($load) {
                $editUrl = route('loads.edit', encode_id($load->id));
                $deleteId = encode_id($load->id);
                $showUrl = route('loads.show', $deleteId);
                return '<a href="' . $showUrl . '" class="">
                            <i class="fa fa-eye table_icon_style blue_icon_color"></i>
                        </a>
                        <a href="' . $editUrl . '" class="">
                            <i class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i>
                        </a>
                        <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                            data-load-id="' . $deleteId . '"></i>';
            })
            ->addColumn('assign', function ($load) {
                return '<a href="'.route('loads.assign', encode_id($load->id)).'" class="btn btn-primary create-button btn_primary_color">
                            <i class="fa-solid fa-user"></i> Assign
                        </a>';
            })
            
            ->rawColumns(['originval', 'destinationval', 'actions', 'assign', 'suppliercompany', 'supplier_company_name']) 
            ->make(true);
        }

        return view('loads.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch origins and destinations
        $origins = Origin::all();
        $destinations = Destination::all();
        $suppliers = Supplier::where('is_active', 1)->get();
        return view('loads.create', compact('origins', 'destinations', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'service_type' => 'required',
            'payer' => 'required',
            'equipment_type' => 'required',
            'trailer_number' => 'nullable',
            'port_of_entry' => 'nullable',
            'supplier_id' => 'nullable',
            'weight' => 'nullable|numeric',
            'delivery_deadline' => 'required|date',
            'customer_po' => 'nullable',
            'schedule' => 'nullable',
            'is_hazmat' => 'boolean',
            'is_inbond' => 'boolean',
        ]);
        $status = 'requested'; 
        $supplier_id = null; 
        $service_id = null;
        $message = 'Load added successfully.';

        // Step 1: Create Load with supplier_id = null
        $load = Load::create(array_merge(
            $request->except('aol_number', 'supplier_id'), 
            [
                'supplier_id' => null, 
                'status' => $status,
                'created_by' => Auth::id(),
                'schedule' => $request->schedule ? Carbon::parse($request->schedule) : null,

            ]
        ));
        
        if ($request->filled('supplier_id') || isSupplierUser()) {
            if(isSupplierUser()){
                $req_sup_id = isSupplierUser();
            }else{
                $req_sup_id =  $request->supplier_id;
            }
            $supplier = Supplier::with('services')->find($req_sup_id);
            if ($supplier) {
                $matchingService = $supplier->services
                    ->where('origin', $request->origin)
                    ->where('destination', $request->destination)
                    ->first();
        
                if ($matchingService) {
                    $status = 'assigned';
                    $service_id = $matchingService->id;
                    $supplier_id = $supplier->id; 
        
                    $load->update([
                        'supplier_id' => $supplier_id,
                        // 'supplier_id' => null,
                        'status' => $status
                    ]);
        
                    AssignedService::create([
                        'load_id' => $load->id,
                        'supplier_id' => $supplier_id,
                        'service_id' => $service_id,
                    ]);
                }else{
                    $message = sprintf(
                        'Load added successfully. No Matching Service Found With Supplier of company ',
                        $supplier->company_name
                    );
                }
            }
        }  
        return redirect()->route('loads.index')->with('message', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $en = $id;
        $de = decode_id($id);
        $load = Load::with(['supplierdata', 'assignedServices.supplier', 'assignedServices.service', 'origindata', 'destinationdata',])->findOrFail($de);

        return view('loads.show', compact('load'));
    }

    public function edit($id)
    {
        $en = $id;
        $de = decode_id($id);
        $load = Load::with(['origindata', 'destinationdata'])->findOrFail($de);
        $origins = Origin::all(); 
        $destinations = Destination::all();
        $suppliers = Supplier::where('is_active', 1)->get();
        return view('loads.edit', compact('load', 'origins', 'destinations', 'suppliers'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'payer' => 'required|string',
            'equipment_type' => 'required|string',
            'weight' => 'nullable|numeric',
            'delivery_deadline' => 'required|date',
            'service_type' => 'required',
            'supplier_id' => 'nullable',
            'trailer_number' => 'nullable',
            'port_of_entry' => 'nullable',
            'schedule' => 'nullable',
        ]);
    
        $load = Load::findOrFail($id);
        $status = $load->status; 
        $supplier_id = $load->supplier_id; 

        $message = 'Load updated successfully.';
      
        if ($request->filled('supplier_id')) {
            $supplier = Supplier::with('services')->find($request->supplier_id);
            // dd($supplier);

            if ($supplier) {
                $matchingService = $supplier->services
                    ->where('origin', $request->origin)
                    ->where('destination', $request->destination)
                    ->first();
        
                if ($matchingService) {
                    $status = 'assigned';
                    $service_id = $matchingService->id; 
        
                    AssignedService::create([
                        'load_id' => $load->id,
                        'supplier_id' => $request->supplier_id,
                        'service_id' => $service_id,
                    ]);
                    $supplier_id = $request->supplier_id; 
                } else {
                    $message = sprintf(
                        'Load updated successfully. No Matching Service Found With Supplier of company ',
                        $supplier->company_name
                    );
                }
            }
        }

        $load->update([
            'origin' => $request->origin,     
            'destination' => $request->destination,
            'payer' => $request->payer,
            'equipment_type' => $request->equipment_type,
            'service_type' => $request->service_type,
            'weight' => $request->weight,
            'delivery_deadline' => $request->delivery_deadline,
            'customer_po' => $request->customer_po,
            'is_hazmat' => $request->has('is_hazmat'),
            'is_inbond' => $request->has('is_inbond'),
            'trailer_number' => $request->trailer_number,
            'schedule' => $request->schedule ? Carbon::parse($request->schedule) : null,
            'port_of_entry' => $request->port_of_entry,
            // 'supplier_id' => null,
            'supplier_id' => $supplier_id,
            'status' => $status,
        ]);
    
        return redirect()->route('loads.index')->with('message',$message);
    }

    public function destroy($id)
    {
        $en = $id;
        $de = decode_id($id);
        $load = Load::find($de);
        if (!$load) {
            return redirect()->route('loads.index')->with('error', 'Load not found.');
        }
        $load->delete();
        return redirect()->route('loads.index')->with('meassge', 'Load deleted successfully.');

    }

    public function assignPage($id)
{
    $en = $id;
    $de = decode_id($id);
    $load = Load::findOrFail($de);
    $assignedServiceIds = AssignedService::where('load_id', $load->id)->pluck('service_id');
    $suppliers = Supplier::whereHas('services', function ($query) use ($load, $assignedServiceIds) {
        $query->where('origin', $load->origin)
              ->where('destination', $load->destination)
              ->whereNotIn('id', $assignedServiceIds);
    })->with(['services' => function ($query) use ($load, $assignedServiceIds) {
        $query->where('origin', $load->origin)
              ->where('destination', $load->destination)
              ->whereNotIn('id', $assignedServiceIds)
              ->orderBy('cost', 'asc'); 
    }])->get();

    $deletedAssignedServices = AssignedService::onlyTrashed()
    ->where('load_id', $load->id)
    ->with(['supplier', 'service'])
    ->get();

    $assignedServices = AssignedService::where('load_id', $load->id)
    ->with(['supplier', 'service'])
    ->get();

    $remainingSuppliers = Supplier::whereHas('services')
    ->with(['services' => function ($query) use ($load) {
        $query->where('origin', '!=', $load->origin)
        ->orWhere('destination', '!=', $load->destination)->orderBy('cost', 'asc');       
    }])->where('is_active', 1)
    ->get();

    // dd($remainingSuppliers);
    return view('loads.assign', compact('load', 'suppliers','remainingSuppliers', 'assignedServices', 'deletedAssignedServices'));
}

    // Assign Supplier to Load
    public function assignSupplier($load_id, $supplier_id, $service_id)
    {
        $enload = $load_id;
        $deLoad = decode_id($load_id);
        $load = Load::findOrFail($deLoad);
        $load->supplier_id = decode_id($supplier_id);
        $load->status = 'assigned';

        $load->save();

        return redirect()->back()->with('message', 'Supplier assigned successfully.');
    }
    

    public function assign(Request $request)
    {
        // Validate input
        $request->validate([
            'load_id' => 'required|exists:loads,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'service_id' => 'required|exists:services,id',
        ]);

        // Check if service is already assigned to the load
        $existingAssignment = AssignedService::where([
            'load_id' => $request->load_id,
            'service_id' => $request->service_id
        ])->first();

        if ($existingAssignment) {
            return redirect()->back()->with('error', 'This service is already assigned.');
        }

        // Assign the service
        AssignedService::create([
            'load_id' => $request->load_id,
            'supplier_id' => $request->supplier_id,
            'service_id' => $request->service_id,
        ]);
        Load::where('id', $request->load_id)->update(['status' => 'assigned']);
        return redirect()->back()->with('message', 'Service assigned successfully.');
    }

    public function unassignService(Request $request,$id)
    {
        $assignedService = AssignedService::find($id);
        if ($assignedService) {
            $load_id = $assignedService->load_id; 
            // Capture the reason for unassignment
            $reason = $request->unassign_reason;
            if ($reason === 'Other') {
                $reason = $request->other_reason; // Use custom input if "Other" was selected
            }

            // Soft delete with cancellation reason
            $assignedService->update([
                'cancel_reason' => $reason
            ]);

            // dd("dsuyfduy");

            $assignedService->delete();
    
            $remainingServices = AssignedService::where('load_id', $load_id)->exists();
    
            if (!$remainingServices) {
                Load::where('id', $load_id)->update(['status' => 'requested', 'supplier_id' => null]);
            }

        return back()->with('error', 'Service not found.');
        }
    }

}
