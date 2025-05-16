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
use App\Models\ClientCost;
use App\Models\ClientService;
use App\Models\SupplierService;

class LoadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request)
    {
        $user = auth()->user();
        // dd($user);
        $userType = $user->roledata->user_type_id;
        $company_name = User::where('id', $user->client_id)->value('business_name');
        $sup_company_name = Supplier::where('id', $user->supplier_id)->value('company_name');


        if ($request->ajax()) {
            $aolNumber = $request->input('aol_number');
            $status = $request->input('status');
            $shipment_status_filter = $request->input('shipment_status_filter');
            $creatorFilter = $request->input('creator_filter');
            $clientFilter = $request->input('client_filter');

            $loads = Load::with(['origindata', 'destinationdata', 'supplierdata', 'assignedServices.supplier', 'creator', 'creatorfor'])
            ->when($userType == 2, function ($query) use ($user, $company_name) {
                return $query->where(function ($q) use ($user, $company_name) {
                    $q->where(function ($q1) use ($user) {
                        $q1->where('created_by', $user->id)
                            ->where(function ($q2) {
                                $q2->whereNull('schedule')
                                    ->orWhereDate('schedule', '<=', now());
                            });
                    })
                    ->orWhereHas('creatorfor', function ($q3) use ($company_name) {
                        $q3->where('business_name', $company_name);
                    });
                               
                });
            })
                
            ->when($userType == 3, function ($query) use ($user) {
                return $query->where(function ($q) use ($user) {
                    $q->whereHas('assignedServices', function ($q1) use ($user) {
                        $q1->whereHas('supplier', function ($q2) use ($user) {
                            $q2->where('id', $user->supplier_id);
                        });
                    })
                    ->orWhereHas('creator', function ($q3) use ($user) {
                        $q3->where('supplier_id', $user->supplier_id);
                    })
                    ->orWhere('created_by', $user->id)
                    ->orWhere('status', 'requested');
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
                    if (is_array($status) && !empty($status)) {
                        return $query->whereIn('status', $status);
                    }
                    return $query;
                })
                ->when(!empty($creatorFilter), function ($query) use ($creatorFilter) {
                    return $query->whereIn('created_by', (array) $creatorFilter); 
                })
                ->when(!empty($clientFilter), function ($query) use ($clientFilter) {
                    return $query->whereIn('created_for', (array) $clientFilter); 
                })
                ->when($shipment_status_filter, function ($query) use ($shipment_status_filter) {
                    return $query->where('shipment_status', $shipment_status_filter);
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
                    return '
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
                ->addColumn('add_invoice', function ($load) {
                    if ($load->status === 'assigned') {
                    return '<a href="' . route('upload.bill.form', ['load_id' => encode_id($load->id)]) . '" 
                                class="btn btn-primary create-button btn_primary_color">
                                <i class="fa-solid fa-user"></i> '. __('messages.add_invoice').'
                            </a>';
                        } else {
                            return '<span>NA</span>';
                        }
                })
                ->addColumn('quickbooks_invoice', function ($load) {
                    if ($load->invoice_id) {
                    return '<a href="' . route('loads.quickbooks_invoices', ['load_id' => encode_id($load->id)]) . '" 
                                class="btn btn-primary create-button btn_primary_color qb_invoice">
                                      <img src="/assets/img/qb1.png">
                                 
                            </a>';
                        } else {
                            // If supplier_invoice_id is null, return 'NA'
                            return '<span>---</span>';
                        }
                })
                ->addColumn('quickbooks_supplier_invoice', function ($load) {
                    // Check if supplier_invoice_id exists and is not null
                    if ($load->supplier_invoice_id) {
                        // If it exists, show the button with the link to view the supplier invoice
                        return '<a href="' . route('invoice.supplier', ['load_id' => encode_id($load->id)]) . '" 
                                    class="btn btn-primary create-button btn_primary_color qb_invoice">
                                   <img src="/assets/img/qb1.png">
                                </a>';
                    } else {
                        // If supplier_invoice_id is null, return 'NA'
                        return '<span>---</span>';
                    }
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
                ->rawColumns(['originval', 'destinationval', 'actions', 'assign', 'suppliercompany', 'supplier_company_name', 'shipment_status', 'update_details', 'aol', 'add_invoice', 'quickbooks_invoice', 'quickbooks_supplier_invoice']) 

                ->make(true);
        }

        $creators = Load::with('creator')
        ->selectRaw('DISTINCT created_by')
        ->whereNotNull('created_by') 
        ->get();

        // $creatorsclients = Load::with('creator.client') 
        // ->whereHas('creator', function ($query) {
        //     $query->whereNotNull('client_id')
        //         ->orWhere('is_client', 1);
        // })
        // ->selectRaw('DISTINCT created_by')
        // ->whereNotNull('created_by')
        // ->get();

        $creatorsclients = Load::with('creatorfor')
        ->selectRaw('DISTINCT created_for')
        ->whereNotNull('created_for') 
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
           'customer_po' => 'nullable|array',
            'customer_po.*' => 'nullable|string|max:255',
            'schedule' => 'nullable',
            'is_hazmat' => 'boolean',
            'is_inbond' => 'boolean',
            'client_id' => 'required|exists:users,id',
            'inspection' => 'boolean',
            'notes' => 'nullable',
        ]);
        $status = 'requested'; 
        $supplier_id = null; 
        $service_id = null;

        // Step 1: Create Load with supplier_id = null
        $referenceNumbers = array_filter($request->customer_po ?? []); 

        $load = Load::create(array_merge(
            $request->except('aol_number', 'supplier_id'), 
            [
                'supplier_id' => null, 
                'created_for' => $request->client_id, 
                'status' => $status,
                'shipment_status' => 'pending',
                'created_by' => Auth::id(),
                'schedule' => $request->schedule ? Carbon::parse($request->schedule) : null,
                'customer_po' => implode(', ', $referenceNumbers)
            ]
        ));
        $message = __('messages.Load added successfully with Aol Number: :id', ['id' => $load->aol_number]);

        
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

        // Get the client user
        $client = User::find($request->client_id);

        if ($client && $client->email) {
            // Send email
        if (isEmailTypeActive('load_created_notification')) {

            queueEmailJob(
                recipients: [$client->email],
                subject: 'New Load Created - ' . config('app.name'),
                template: 'emails.load_created_notification',
                payload: [
                    'email' => $client->email,
                    'company_name' => $client->business_name ?? 'your company',
                ],
                emailType: 'load_created_notification'
            );
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
        // $assignedServices = AssignedService::where('load_id', $load->id)
        // ->with(['supplier', 'service.masterService'])
        // ->get();
        $load = Load::with(['supplierdata', 'assignedServices.supplier', 'assignedServices.service', 'assignedServices.service.masterService', 'origindata', 'destinationdata',])->findOrFail($de);

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
            'reefer_temperature' => 'nullable',
            'customer_po' => 'nullable|array',
            'customer_po.*' => 'nullable|string|max:255',
            'notes' => 'nullable',
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
            $referenceNumbers = array_filter($request->customer_po ?? []);
        $load->update([
            'origin' => $request->origin,     
            'destination' => $request->destination,
            'payer' => $request->payer,
            'equipment_type' => $request->equipment_type,
            'service_type' => $request->service_type,
            'weight' => $request->weight,
            'delivery_deadline' => $request->delivery_deadline,
            'customer_po' => implode(', ', $referenceNumbers),
            'is_hazmat' => $request->has('is_hazmat'),
            'inspection' => $request->has('inspection'),
            'is_inbond' => $request->has('is_inbond'),
            'trailer_number' => $request->trailer_number,
            'schedule' => $request->schedule ? Carbon::parse($request->schedule) : null,
            'port_of_entry' => $request->port_of_entry,
            // 'supplier_id' => null,
            'supplier_id' => $supplier_id,
            'status' => $status,
            'weight_unit' => $request->weight_unit ,
            'created_for' => $request->client_id, 
            'reefer_temperature' => $request->reefer_temperature ?? null ,
            'notes' => $request->notes, 
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
    // dd("dfjdhj");
    $en = $id;
    $de = decode_id($id);
    $load = Load::with(['creatorfor'])->findOrFail($de);
    $clientId = $load->created_for;
    $assignedServiceIds = AssignedService::where('load_id', $load->id)->pluck('service_id');

    // Get filter inputs
    $supplierId = $request->input('supplier_id');
    $serviceType = $request->input('service_type');
    $suppliers = collect();
    if ($supplierId || $serviceType) {
    // Query suppliers with optional filters
    // $suppliers = Supplier::when($supplierId, function ($query) use ($supplierId) {

    //     return $query->where('id', $supplierId);
    // }, function ($query) use ($load, $assignedServiceIds,$serviceType) {
    //     if (!$serviceType) {

    //         return $query->whereHas('services', function ($subQuery) use ($load, $assignedServiceIds) {
    //             $subQuery->where('origin', $load->origin)
    //                 ->where('destination', $load->destination);
    //         });
    //     }
    // })
    // ->with(['services' => function ($query) use ($serviceType, $supplierId, $load) {
    //     if (!$serviceType &&  !$supplierId) {
    //             $query->where('origin', $load->origin)
    //                 ->where('destination', $load->destination);
    //         }else{
    //             if ($serviceType) {
    
    //                 $query->where('service_type', $serviceType); 
    //             }
    //         }
    // }, 'services.clientCosts' => function ($query) use ($load, $supplierId, $clientId) {
    //     $query->where('client_id', $clientId);
    // }])->where('is_active', 1)
    // ->get();
    // dd($suppliers );
    // $suppliers = Supplier::when($supplierId, function ($query) use ($supplierId) {
    //     // Filter by specific supplier if supplierId is provided
    //     return $query->where('id', $supplierId);
    // }, function ($query) use ($load, $serviceType) {
    //     // Filter by origin and destination if service type is not provided
    //     if (!$serviceType) {
    //         return $query->whereHas('supplierServices.masterService', function ($subQuery) use ($load) {
    //             $subQuery->where('origin', $load->origin)
    //                      ->where('destination', $load->destination);
    //         });
    //     }
    // })
    // ->with([
    //     'supplierServices' => function ($query) use ($serviceType, $supplierId, $load) {
    //         if (!$serviceType && !$supplierId) {
    //             $query->whereHas('masterService', function ($subQuery) use ($load) {
    //                 $subQuery->where('origin', $load->origin)
    //                          ->where('destination', $load->destination);
    //             });
    //         } elseif ($serviceType) {
    //             $query->whereHas('masterService', function ($subQuery) use ($serviceType) {
    //                 $subQuery->where('service_type', $serviceType);
    //             });
    //         }
    //     },
    //     'supplierServices.masterService', // Include master service details
    //     'supplierServices.clientServices' => function ($query) use ($clientId) {
    //         // Filter client services for the current client
    //         $query->where('client_id', $clientId)
    //               ->select(['id', 'master_service_id', 'cost']);
    //     }
    // ])
    // ->where('is_active', 1)
    // ->get();

    $suppliers = Supplier::when($supplierId, function ($query) use ($supplierId) {
        // Filter by supplier ID
        return $query->where('id', $supplierId);
    })
    ->when($serviceType, function ($query) use ($serviceType) {
        // Filter by service type
        return $query->whereHas('supplierServices.masterService', function ($subQuery) use ($serviceType) {
            $subQuery->where('service_type', $serviceType);
        });
    })
    ->when(!$supplierId && !$serviceType, function ($query) use ($load) {
        // Filter by origin and destination if no specific supplier or service type is selected
        return $query->whereHas('supplierServices.masterService', function ($subQuery) use ($load) {
            $subQuery->where('origin', $load->origin)
                     ->where('destination', $load->destination);
        });
    })
    ->with([
        'supplierServices' => function ($query) use ($supplierId, $serviceType, $load) {
            if ($supplierId || $serviceType) {
                // Filter by service type or supplier ID
                if ($serviceType) {
                    $query->whereHas('masterService', function ($subQuery) use ($serviceType) {
                        $subQuery->where('service_type', $serviceType);
                    });
                }

                if ($supplierId) {
                    $query->where('supplier_id', $supplierId);
                }
            } else {
                // Default load filter if no supplier ID or service type is provided
                $query->whereHas('masterService', function ($subQuery) use ($load) {
                    $subQuery->where('origin', $load->origin)
                             ->where('destination', $load->destination);
                });
            }
        },
        'supplierServices.masterService',
        'supplierServices.clientServices' => function ($query) use ($clientId) {
            // Filter by client ID for client costs
            $query->where('client_id', $clientId);
        }
    ])
    ->where('is_active', 1)
    ->get();
    // dd($suppliers );
    }
    $deletedAssignedServices = AssignedService::onlyTrashed()
        ->where('load_id', $load->id)
        ->with(['supplier', 'service.masterService'])
        ->get();

    $assignedServices = AssignedService::where('load_id', $load->id)
        ->with(['supplier', 'service.masterService'])
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
        // Validate input
        $request->validate([
            'load_id' => 'required|exists:loads,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'service_id' => 'required|exists:master_services,id',
            'supplier_service_id' => 'required|exists:supplier_services,id',
            'quantity' => 'nullable|numeric',
        ]);

        // Check if service is already assigned to the load
        $existingAssignment = AssignedService::where([
            'load_id' => $request->load_id, 
            'service_id' => $request->service_id
        ])->first();

        if ($existingAssignment) {
            return redirect()->back()->with('error',  __('messages.This service is already assigned.'));
        }


        $loadData = Load::findOrFail($request->load_id);
        
        $supplier_cost = SupplierService::where([
            'id' => $request->supplier_service_id,
        ])->value('cost');

        $cost = ClientService::where([
            'client_id' => $loadData->created_for,
            'master_service_id' => $request->service_id
        ])->value('cost');

        if (is_null($cost) || $cost == 0 || $cost == '0.00') {
            return redirect()->back()->with('error', __('messages.client_cost_missing'));
        }

        // Assign the service
        AssignedService::create([
            'load_id' => $request->load_id,
            'supplier_id' => $request->supplier_id,
            'service_id' => $request->supplier_service_id,
            'quantity' => $request->quantity ?? 1,
            'cost' => $cost,
            'supplier_cost' => $supplier_cost,

        ]);
        $load = Load::where('id', $request->load_id)->update(['status' => 'assigned']);
        $supplier_detail = Supplier::findOrFail($request->supplier_id);
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
        if (isEmailTypeActive('load_assigned')) {

        queueEmailJob(
            recipients: [$supplier_detail->user_email],
            subject: 'New Load Assigned - ' . config('app.name'),
            template: 'emails.load_assigned_to_supplier',
            payload: [
                'email' => $supplier_detail->user_email,
                'company_name' => $supplier_detail->company_name,
                'load_id' => $aol_number,
            ],
            emailType: 'load_assigned'
        );
    }

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

            // $email = Supplier::where('id', $assignedService->supplier_id,)->value('user_email');
            $supplier = Supplier::where('id', $assignedService->supplier_id)->first();
            if ($supplier) {
                $email = $supplier->user_email;
                $company_name = $supplier->company_name;
            }
            $load = Load::where('id', $load_id)->first();
            $aol_number = $load->aol_number;
            $createdForEmail = User::where('id', $load->created_for)->value('email');

            // $aol_number = Load::where('id', $load_id,)->value('aol_number');

            // add log
            UserActivityLog::create([
                'log_type' => UserActivityLog::LOG_TYPE_UNASSIGN_LOAD,
                'description' => 'A load request with AOL number'. ' (' .$aol_number . ') unssigned to  ' 
                        . $email .' by'. auth()->user()->fname . ' ' 
                        . auth()->user()->lname 
                        . ' (' . auth()->user()->email . ')',
                'user_id' => auth()->id(), 
            ]);

             // Example: send cancellation email
        if (isEmailTypeActive('load_cancelled_by_supplier')) {

            queueEmailJob(
                recipients: [$email, $createdForEmail],
                subject: 'Load Cancelled - ' . config('app.name'),
                template: 'emails.load_cancelled_by_supplier',
                payload: [
                    'aol_number' => $aol_number,
                    'reason' => $reason,
                    'company_name' => $company_name,
                ],
                emailType: 'load_cancelled_by_supplier'
            );
        }
            $remainingServices = AssignedService::where('load_id', $load_id)->exists();
    
            if (!$remainingServices) {
                Load::where('id', $load_id)->update(['status' => 'requested', 'supplier_id' => null]);
            }

            // return back()->with('error',  __('messages.Service not found.'));
            return redirect()->back()->with('message',  'Service unassigned successfully');

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

        $client = User::where('id', $load->created_for)->select('email', 'business_name')->first();

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
        if (isEmailTypeActive('shipment_status_updated')) {

        queueEmailJob(
            recipients: [$client->email],
            subject: 'Shipment Status Update - ' . config('app.name'),
            template: 'emails.shipment_status_updated',
            payload: [
                'aol_number' => $aol_number,
                'old_status' => $old_status,
                'new_status' => $load->shipment_status,
                'business_name' => $client->business_name,
            ],
            emailType: 'shipment_status_updated'
        );
    }
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
            'driver_name' => 'nullable|string|max:255',
            'driver_contact_no' => 'nullable|string|max:20',
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
