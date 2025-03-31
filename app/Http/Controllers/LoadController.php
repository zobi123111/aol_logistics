<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Load;
use App\Models\Supplier;
use Yajra\DataTables\DataTables;
use App\Models\Origin;
use App\Models\Destination;
use App\Models\LoadsDocument;
use App\Models\AssignedService;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserActivityLog;

class LoadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request)
    {
        $user = auth()->user();
        $userType = $user->roledata->user_type_id;

        if ($request->ajax()) {
            $aolNumber = $request->input('aol_number');
            $status = $request->input('status');
            $creatorFilter = $request->input('creator_filter');
            $clientFilter = $request->input('client_filter');

            $loads = Load::with(['origindata', 'destinationdata', 'supplierdata', 'assignedServices.supplier', 'creator', 'creatorfor'])
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
                        })->orWhere('created_by', $user->id)->orWhere('status', 'requested');;
                    })
                        ->where(function ($q) {
                            $q->whereNull('schedule')
                                ->orWhereDate('schedule', '<=', now());
                        });
                })
                ->when($aolNumber, function ($query) use ($aolNumber) {
                    return $query->where('aol_number', 'like', '%' . $aolNumber . '%');
                })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when(!empty($creatorFilter), function ($query) use ($creatorFilter) {
                    return $query->whereIn('created_by', (array) $creatorFilter); 
                })
                ->when(!empty($clientFilter), function ($query) use ($clientFilter) {
                    return $query->whereIn('created_by', (array) $clientFilter); 
                })
                ->latest('id')
                ->get();

            return DataTables::of($loads)
                ->addColumn('originval', function ($load) {
                    return $load->origindata
                    ? ($load->origindata->name ?: ($load->origindata->street . ', ' . $load->origindata->city . ', ' . $load->origindata->state . ', ' . $load->origindata->country))
                    : 'N/A';
                        
                })
                ->addColumn('destinationval', function ($load) {
                    return $load->destinationdata
                    ? ($load->destinationdata->name ?: ($load->destinationdata->street . ', ' . $load->destinationdata->city . ', ' . $load->destinationdata->state . ', ' . $load->destinationdata->country))
                    : 'N/A';
                })
                ->addColumn('supplier_company_name', function ($load) {
                    if ($load->assignedServices->isNotEmpty()) {
                        return $load->assignedServices->pluck('supplier.company_name')->filter()->join(', ');
                    }
                    return 'N/A';
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
                            <a href="#" class="delete-icon table_icon_style blue_icon_color" data-load-id="' . $deleteId . '">
                                <i class="fa-solid fa-trash"></i>
                            </a>';
                })
                ->addColumn('assign', function ($load) {
                    return '<a href="' . route('loads.assign', encode_id($load->id)) . '" class="btn btn-primary create-button btn_primary_color">
                                <i class="fa-solid fa-user"></i> ' . __("messages.Assign").'
                            </a>';
                })
                // ->addColumn('add_invoice', function ($load) {
                //     return '<a href="' . route('invoice.upload', encode_id($load->id)) . '" class="btn btn-primary create-button btn_primary_color">
                //                 <i class="fa-solid fa-user"></i> Add Invoice
                //              </a>';
                // })
                ->addColumn('add_invoice', function ($load) {
                    return '<a href="' . route('invoice.client', ['load_id' => encode_id($load->id)]) . '" 
                                class="btn btn-primary create-button btn_primary_color">
                                <i class="fa-solid fa-user"></i> Add Invoice
                            </a>';
                })
                ->addColumn('quickbooks_invoice', function ($load) {
                    return '<a href="' . route('loads.quickbooks_invoices', ['load_id' => encode_id($load->id)]) . '" 
                                class="btn btn-primary create-button btn_primary_color">
                                <i class="fa-solid fa-file-invoice-dollar"></i> View QB Invoice
                            </a>';
                })
                
                ->addColumn('aol', function ($load) {
                    $deleteId = encode_id($load->id);
                    $showUrl = route('loads.show', $deleteId);
                    return '<a href="' . $showUrl . '" >
                                '.$load->aol_number.'
                            </a>';
                })
                ->addColumn('shipment_status', function ($load) {
                    return '<a href="javascript:void(0);" 
                                class="btn btn-primary create-button btn_primary_color" 
                                onclick="changeStatusModal(\'' . encode_id($load->id) . '\', \'' . e($load->shipment_status) . '\')">
                                ' . __('messages.change_status') . '
                            </a>';
                })
                ->addColumn('created_by_user', function ($load) {
                    return optional($load->creator)->fname . ' ' . optional($load->creator)->lname;
                        
                })

                ->addColumn('created_for_user', function ($load) {
                    return optional($load->creatorfor)->business_name ?? optional($load->creatorfor)->email;
                        
                })
                ->addColumn('update_details', function ($load) {
                    return '<a href="' . route('loads.editTruckDetails', encode_id($load->id)) . '" 
                                class="btn btn-primary create-button btn_primary_color">
                                ' . __('messages.update_truck_details') . '
                            </a>';
                })
                ->rawColumns(['originval', 'destinationval', 'actions', 'assign', 'suppliercompany', 'supplier_company_name', 'shipment_status', 'update_details', 'aol', 'add_invoice', 'quickbooks_invoice']) 

                ->make(true);
        }

        $creators = Load::with('creator')
        ->selectRaw('DISTINCT created_by')
        ->whereNotNull('created_by') 
        ->get();

        $creatorsclients = Load::with('creator.client') 
        ->whereHas('creator', function ($query) {
            $query->whereNotNull('client_id')
                ->orWhere('is_client', 1);
        })
        ->selectRaw('DISTINCT created_by')
        ->whereNotNull('created_by')
        ->get();

        return view('loads.index', compact('creators', 'creatorsclients'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        // Fetch origins and destinations
        $origins = Origin::orderBy('name', 'asc')->get();
        $destinations = Destination::orderBy('name', 'asc')->get();
        $suppliers = Supplier::where('is_active', 1)->get();
        $clients = User::where('is_client', 1)->get();
        return view('loads.create', compact('origins', 'destinations', 'suppliers', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
       
        if ($request->filled('delivery_deadline')) {
            $request->merge([
                'delivery_deadline' => Carbon::createFromFormat('M. j, Y', $request->delivery_deadline)->format('Y-m-d'),
            ]);
        }
        
        if ($request->filled('schedule')) {
            $request->merge([
                'schedule' => Carbon::createFromFormat('M. j, Y H:i', $request->schedule)->format('Y-m-d H:i'),
            ]);
        }
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
            'weight_unit' => 'nullable',
            'delivery_deadline' => 'required|date',
            'customer_po' => 'nullable',
            'schedule' => 'nullable',
            'is_hazmat' => 'boolean',
            'is_inbond' => 'boolean',
            'client_id' => 'required|exists:users,id',
        ]);
        $status = 'requested'; 
        $supplier_id = null; 
        $service_id = null;
        $message = __('messages.Load added successfully.');

        // Step 1: Create Load with supplier_id = null
        $load = Load::create(array_merge(
            $request->except('aol_number', 'supplier_id'), 
            [
                'supplier_id' => null, 
                'created_for' => $request->client_id, 
                'status' => $status,
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
            // add log
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_ADD_LOAD,
                'description' => 'A load request with AOL number'. ' (' .$load->aol_number . ') has been created by ' 
                        . auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ')',
                'user_id' => auth()->id(), 
            ]);
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
        $origins = Origin::orderBy('name', 'asc')->get();
        $destinations = Destination::orderBy('name', 'asc')->get();
        $suppliers = Supplier::where('is_active', 1)->get();
        $clients = User::where('is_client', 1)->get();
        return view('loads.edit', compact('load', 'origins', 'destinations', 'suppliers', 'clients'));
    }
    
    public function update(Request $request, $id)
    {
        if ($request->filled('delivery_deadline')) {
            $request->merge([
                'delivery_deadline' => Carbon::createFromFormat('M. j, Y', $request->delivery_deadline)->format('Y-m-d'),
            ]);
        }
        
        if ($request->filled('schedule')) {
            $request->merge([
                'schedule' => Carbon::createFromFormat('M. j, Y H:i', $request->schedule)->format('Y-m-d H:i'),
            ]);
        }
    
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
            'weight_unit' => 'nullable',
            'client_id' => 'required|exists:users,id',
        ]);
    
        $load = Load::findOrFail($id);
        $status = $load->status; 
        $supplier_id = $load->supplier_id; 

        $message =  __('messages.Load updated successfully.');
      
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
            'weight_unit' => $request->weight_unit ,
            'created_for' => $request->client_id, 
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
        return redirect()->route('loads.index')->with('message',  __('messages.Load deleted successfully.'));

    }

    // public function assignPage($id, Request $request)
    // {
    //     $en = $id;
    //     $de = decode_id($id);
    //     $load = Load::findOrFail($de);
    //     $assignedServiceIds = AssignedService::where('load_id', $load->id)->pluck('service_id');
    //     // $suppliers = Supplier::whereHas('services', function ($query) use ($load, $assignedServiceIds) {
    //     //     $query->where('origin', $load->origin)
    //     //         ->where('destination', $load->destination)
    //     //         ->whereNotIn('id', $assignedServiceIds);
    //     // })->with(['services' => function ($query) use ($load, $assignedServiceIds) {
    //     //     $query->where('origin', $load->origin)
    //     //         ->where('destination', $load->destination)
    //     //         ->whereNotIn('id', $assignedServiceIds)
    //     //         ->orderBy('cost', 'asc'); 
    //     // }])->get();

    //       // Get Supplier ID from request (if provided)
    // $supplierId = $request->input('supplier_id');
    //      // Adjust supplier query based on supplier_id presence
    // $suppliers = Supplier::when($supplierId, function ($query) use ($supplierId) {
    //     return $query->where('id', $supplierId);
    // }, function ($query) use ($load, $assignedServiceIds) {
    //     return $query->whereHas('services', function ($subQuery) use ($load, $assignedServiceIds) {
    //         $subQuery->where('origin', $load->origin)
    //             ->where('destination', $load->destination);
    //             // ->whereNotIn('id', $assignedServiceIds);
    //     });
    // })
    // ->with(['services' => function ($query) use ($load, $assignedServiceIds, $supplierId) {
    //     if (!$supplierId) {
    //         $query->where('origin', $load->origin)
    //             ->where('destination', $load->destination);
    //             // ->whereNotIn('id', $assignedServiceIds);
    //     }
    //     $query->orderBy('cost', 'asc');
    // }])->where('is_active', 1)
    // ->get();

    //     $deletedAssignedServices = AssignedService::onlyTrashed()
    //     ->where('load_id', $load->id)
    //     ->with(['supplier', 'service'])
    //     ->get();

    //     $assignedServices = AssignedService::where('load_id', $load->id)
    //     ->with(['supplier', 'service'])
    //     ->get();

    //     // $remainingSuppliers = Supplier::whereHas('services')
    //     // ->with(['services' => function ($query) use ($load) {
    //     //     $query->where('origin', '!=', $load->origin)
    //     //     ->orWhere('destination', '!=', $load->destination)->orderBy('cost', 'asc');       
    //     // }])->where('is_active', 1)
    //     // ->get();
    //     $allSuppliers = Supplier::with('services')->where('is_active', 1)->get();
    //     // dd($remainingSuppliers);
    //     // return view('loads.assign', compact('load', 'suppliers','remainingSuppliers', 'assignedServices', 'deletedAssignedServices', 'allSuppliers'));
    //     return view('loads.assign', compact('load', 'suppliers', 'assignedServices', 'deletedAssignedServices', 'allSuppliers'));

    // }


    public function assignPage($id, Request $request)
{
    $en = $id;
    $de = decode_id($id);
    $load = Load::findOrFail($de);
    $assignedServiceIds = AssignedService::where('load_id', $load->id)->pluck('service_id');

    // Get filter inputs
    $supplierId = $request->input('supplier_id');
    $serviceType = $request->input('service_type');

    // Query suppliers with optional filters
    $suppliers = Supplier::when($supplierId, function ($query) use ($supplierId) {

        return $query->where('id', $supplierId);
    }, function ($query) use ($load, $assignedServiceIds,$serviceType) {
        if (!$serviceType) {

            return $query->whereHas('services', function ($subQuery) use ($load, $assignedServiceIds) {
                $subQuery->where('origin', $load->origin)
                    ->where('destination', $load->destination);
            });
        }
    })
    ->with(['services' => function ($query) use ($load, $assignedServiceIds, $supplierId, $serviceType) {
        if (!$serviceType &&  !$supplierId) {
            $query->where('origin', $load->origin)
                ->where('destination', $load->destination);
        }else{
            if ($serviceType) {

                $query->where('service_type', $serviceType); 
            }
        }
        $query->orderBy('cost', 'asc');
    }])->where('is_active', 1)
    ->get();
// dd($suppliers, "sdbjcfjh");

    $deletedAssignedServices = AssignedService::onlyTrashed()
        ->where('load_id', $load->id)
        ->with(['supplier', 'service'])
        ->get();

    $assignedServices = AssignedService::where('load_id', $load->id)
        ->with(['supplier', 'service'])
        ->get();

    $allSuppliers = Supplier::with('services')->where('is_active', 1)->get();

    return view('loads.assign', compact('load', 'suppliers', 'assignedServices', 'deletedAssignedServices', 'allSuppliers'));
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

        return redirect()->back()->with('message', __('messages.Supplier assigned successfully.'));
    }
    

    public function assign(Request $request)
    {
        // dd( $request);
        // Validate input
        $request->validate([
            'load_id' => 'required|exists:loads,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'nullable|numeric',
        ]);

        // Check if service is already assigned to the load
        $existingAssignment = AssignedService::where([
            'load_id' => $request->load_id, 
            'service_id' => $request->service_id
        ])->first();

        // if ($existingAssignment) {
        //     return redirect()->back()->with('error',  __('messages.This service is already assigned.'));
        // }
        $cost = Service::where([
            'id' => $request->service_id,
            'supplier_id' => $request->supplier_id
        ])->value('cost');
        // Assign the service
        AssignedService::create([
            'load_id' => $request->load_id,
            'supplier_id' => $request->supplier_id,
            'service_id' => $request->service_id,
            'quantity' => $request->quantity ?? 1,
            'cost' => $cost
        ]);
        $load = Load::where('id', $request->load_id)->update(['status' => 'assigned']);
        $email = Supplier::where('id', $request->supplier_id,)->value('user_email');
        $aol_number = Load::where('id', $request->load_id,)->value('aol_number');

         // add log
         UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_ASSIGN_LOAD,
            'description' => 'A load request with AOL number'. ' (' .$aol_number . ') assigned to  ' 
                    . $email .' by '
                    . auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ')',
            'user_id' => auth()->id(), 
        ]);

        return redirect()->back()->with('message',  __('messages.Service assigned successfully.'));
    }

    public function unassignService(Request $request,$id)
    {
        $assignedService = AssignedService::find($id);
        if ($assignedService) {
            $load_id = $assignedService->load_id; 
            // Capture the reason for unassignment
            $reason = $request->unassign_reason;
            if ($reason === 'Other') {
                $reason = $request->other_reason;
            }

            // Soft delete with cancellation reason
            $assignedService->update([
                'cancel_reason' => $reason
            ]);

            $assignedService->delete();

            $email = Supplier::where('id', $assignedService->supplier_id,)->value('user_email');
            $aol_number = Load::where('id', $load_id,)->value('aol_number');

            // add log
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_UNASSIGN_LOAD,
                'description' => 'A load request with AOL number'. ' (' .$aol_number . ') unssigned to  ' 
                        . $email .' by'. auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ')',
                'user_id' => auth()->id(), 
            ]);

            $remainingServices = AssignedService::where('load_id', $load_id)->exists();
    
            if (!$remainingServices) {
                Load::where('id', $load_id)->update(['status' => 'requested', 'supplier_id' => null]);
            }


        return back()->with('error',  __('messages.Service not found.'));
        }
    }

    public function changeStatus(Request $request, $encodedId)
    {
        $id = decode_id($encodedId); 
    
        $request->validate([
            'status' => 'required',
        ]);
        $load = Load::findOrFail($id);
        $old_status = $load->shipment_status;
        $load->shipment_status = $request->status;
        $load->save();

        if ($request->status === 'ready_to_invoice') {
            // Check if an invoice already exists for this load_id
            $existingInvoice = Invoice::where('load_id', $load->id)->exists();
        
            if (!$existingInvoice) {
                Invoice::create([
                    'load_id' => $load->id,
                    'status' => 'pending', 
                    'external_invoice_id' => null, 
                ]);
            }
        }

        $aol_number = Load::where('id', $load->id,)->value('aol_number');

        // add log
        UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_LOAD_STATUS_CHANGE,
            'description' => 'A load request with AOL number'. ' (' .$aol_number . ') shipment status changed from '. $old_status .' to '. $load->shipment_status .' by ' 
                    . auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ')' ,
            'user_id' => auth()->id(), 
        ]);
        return redirect()->back()->with('message', __('messages.load_status_updated'));
    }

    public function editTruckDetails($id)
    {
        $id = decode_id($id); 
        $load = Load::findOrFail($id);
        return view('loads.edit_truck_details', compact('load'));
    }

    public function updateTruckDetails(Request $request, $id)
    {
        $request->validate([
            'truck_number' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
            'driver_contact_no' => 'required|string|max:20',
            'documents.*' => 'file|mimes:pdf,jpg,png|max:2048',
        ]);

        $load = Load::findOrFail($id);
        $load->truck_number = $request->truck_number;
        $load->driver_name = $request->driver_name;
        $load->driver_contact_no = $request->driver_contact_no;
        $load->save();

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('load_documents', 'public');

                LoadsDocument::create([
                    'load_id' => $load->id,
                    'document_type' => 'truck_document',
                    'path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('message', __('messages.truck_updated'));
    }

    public function deleteDocument($id)
    {
        $id = decode_id($id); 
        $document = LoadsDocument::findOrFail($id);
        $document->delete();
        return redirect()->back()->with('message', __('messages.document_deleted'));
    }
    
    public function markAsDelivered($loadId)
{
    $load = Load::findOrFail($loadId);
    $load->update(['status' => 'delivered']);

    // Sync invoice to QuickBooks
    return app(QuickBooksController::class)->syncLoadInvoice($loadId);
}
}
