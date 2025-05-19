<?php

namespace App\Http\Controllers;

use App\Models\SupplierService;
use App\Models\Supplier;
use App\Models\MasterService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SupplierServiceController extends Controller
{

public function index(Request $request, $supplierId)
{
    $de_supplier_id = decode_id($supplierId);
    $supplier = Supplier::findOrFail($de_supplier_id);

    // Handle AJAX request for DataTables
    if ($request->ajax()) {
        $services = SupplierService::where('supplier_id', $de_supplier_id)
            ->with(['masterService.origindata', 'masterService.destinationdata'])
            ->select('supplier_services.*');

        return datatables()->eloquent($services)
        ->addColumn('service_name', function ($service) {
            $masterService = $service->masterService;

            if ($masterService->service_type === 'freight') {
                $origin = $masterService->origindata 
                    ? $masterService->origindata->name 
                    : "{$masterService->street}, {$masterService->city}, {$masterService->state}, {$masterService->country}";

                $destination = $masterService->destinationdata 
                    ? $masterService->destinationdata->name 
                    : "{$masterService->street}, {$masterService->city}, {$masterService->state}, {$masterService->country}";

                return "{$masterService->service_name} (Origin: $origin, Destination: $destination)";
            } elseif ($masterService->service_type === 'warehouse') {
                return "{$masterService->service_name} ({$masterService->street}, {$masterService->city}, {$masterService->state}, {$masterService->zip}, {$masterService->country})";
            }

            return $masterService->service_name ?? 'N/A';
        })
            ->addColumn('cost', function ($service) {
                return '$' . number_format($service->cost, 2);
            })
            // ->addColumn('service_date', function ($service) {
            //     return $service->service_date->format('Y-m-d');
            // })
            ->addColumn('actions', function ($service) use ($supplier) {
                return '<a href="' . route('supplier_services.edit', [encode_id($supplier->id), encode_id($service->id)]) . '" class="table_icon_style blue_icon_color">
                            <i class="fa fa-edit"></i>
                        </a>
                       <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                        data-supplier-id="' . encode_id($supplier->id) . '" data-service-id="' . encode_id($service->id) . '"></i>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    return view('supplier-services.index', compact('supplier'));
}


    // Show the form for creating a new Supplier Service
    public function create($supplierId)
    {
        $en = $supplierId;
        $de_supplier_id = decode_id($supplierId);
        $supplier = Supplier::findOrFail($de_supplier_id);
        $serviceTypes = MasterService::select('service_type')->distinct()->pluck('service_type');
        $masterServices = MasterService::with(['origindata', 'destinationdata'])
        ->get()
        ->groupBy('service_type');    
        return view('supplier-services.create', compact('supplier', 'serviceTypes', 'masterServices'));
    }

    // Store a newly created Supplier Service
    public function store(Request $request, $supplierId)
    {
        $request->validate([
            'master_service_id' => 'required|exists:master_services,id',
            'cost' => 'required|numeric|min:0',
            'service_date' => 'nullable|date|after:today',
            'schedule_cost' => 'nullable|numeric|min:0',
            'service_type' => 'required',
        ]);

        $serviceDate = $request->service_date ? date('Y-m-d', strtotime($request->service_date)) : null;

          // Find existing SupplierService record with same supplier_id, service_date, and master_service_id
            $existingService = SupplierService::where('supplier_id', $supplierId)
            ->where('master_service_id', $request->master_service_id)
            ->first();

        if ($existingService) {
        // If record exists, update the cost
        return redirect()->back()->withErrors([
            'master_service_id' => 'A service with the same supplier and master service already exists.'
        ])->withInput();
        } else {
        // If no record exists, create a new one
        SupplierService::create([
        'supplier_id' => $supplierId,
        'master_service_id' => $request->master_service_id,
        'cost' => $request->cost,
        'service_date' => $serviceDate,
        'schedule_cost' => $request->schedule_cost,
        ]);
        }

        return redirect()->route('supplier_services.index', encode_id($supplierId))->with('message', 'Supplier Service created successfully.');
    }

    // Show the form for editing a Supplier Service
    public function edit($supplierId, $supplierServiceId)
    {
        $de_supplier_id = decode_id($supplierId);
        $de_supplier_service_id = decode_id($supplierServiceId);
    
        $supplier = Supplier::findOrFail($de_supplier_id);
        $supplierService = SupplierService::with(['masterService.origindata', 'masterService.destinationdata'])
            ->findOrFail($de_supplier_service_id);
    
        // Group Master Services by service type for the dropdown
        $masterServices = MasterService::with(['origindata', 'destinationdata'])
            ->get()
            ->groupBy('service_type');
    
        // Get all available service types
        $serviceTypes = $masterServices->keys();
    
        return view('supplier-services.edit', compact('supplier', 'supplierService', 'masterServices', 'serviceTypes'));
    }

    // Update a Supplier Service
    public function update(Request $request, $supplierId, $serviceId)
    {
        // dd("dhbhjd");
        $request->validate([
            'master_service_id' => 'required|exists:master_services,id',
            'cost' => 'required|numeric|min:0',
            'service_date' => 'nullable|date|after:today',
            'service_type' => 'required',
        ]);

    // Check for existing record with the same supplier and master service
    $existingService = SupplierService::where('supplier_id', $supplierId)
        ->where('master_service_id', $request->master_service_id)
        ->where('id', '!=', $serviceId)
        ->first();

    if ($existingService) {
        // Redirect back with an error if a duplicate is found
        return redirect()->back()->withErrors([
            'master_service_id' => 'A service with the same supplier and master service already exists.'
        ])->withInput();
    }
        $supplierService = SupplierService::where('supplier_id', $supplierId)->findOrFail($serviceId);
        $supplierService->update([
            'master_service_id' => $request->master_service_id,
            'cost' => $request->cost,
            'service_date' => $request->service_date,
            'schedule_cost' => $request->schedule_cost,
            'service_type' => $request->service_type,
        ]);
        return redirect()->route('supplier_services.index', encode_id($supplierId))->with('message', 'Supplier Service updated successfully.');
    }

    // Delete a Supplier Service
    public function destroy($supplierId, $serviceId)
    {
        $de_supplier_id = decode_id($supplierId);
        $de_supplier_service_id = decode_id($serviceId);
        $supplierService = SupplierService::where('supplier_id', $de_supplier_id)->findOrFail($de_supplier_service_id);
        $supplierService->delete();

        return redirect()->route('supplier_services.index', $supplierId)->with('message', 'Supplier Service deleted successfully.');
    }
}
