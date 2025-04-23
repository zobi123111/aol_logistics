<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Supplier;
use App\Models\Origin;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ServiceController extends Controller
{
    // Show all services for the supplier
    // public function index($supplier_id)
    // {
    //     $de_supplier_id = decode_id($supplier_id);
    //     $supplier = Supplier::findOrFail($de_supplier_id);
    //     $services = Service::where('supplier_id', $de_supplier_id)
    //     ->with(['origindata', 'destinationdata'])
    //     ->get();
    //     $origins = Origin::all();
    //     $destinations = Destination::all();
    //     return view('services.index', compact('supplier', 'services', 'origins', 'destinations'));
    // }

    public function index(Request $request, $supplier_id)
{
    $de_supplier_id = decode_id($supplier_id);
    $supplier = Supplier::findOrFail($de_supplier_id);

    if ($request->ajax()) {
        $services = Service::where('supplier_id', $de_supplier_id)
            ->with(['origindata', 'destinationdata'])
            ->select('services.*');

        return datatables()->eloquent($services)
            ->addColumn('origin', function ($service) {
                return $service->service_type !== 'warehouse' && $service->origindata
                ? ($service->origindata->name ?: ($service->origindata->street . ', ' . $service->origindata->city . ', ' . $service->origindata->state . ', ' . $service->origindata->country))
                : 'NA';
            })
            ->addColumn('destination', function ($service) {
                return $service->service_type !== 'warehouse' && $service->destinationdata
                ? ($service->destinationdata->name ?: ($service->destinationdata->street . ', ' . $service->destinationdata->city . ', ' . $service->destinationdata->state . ', ' . $service->destinationdata->country))
                : 'NA';
            })
            ->addColumn('warehouse', function ($service) {
                if ($service->service_type === 'warehouse') {
                    return "{$service->street}, {$service->city}, {$service->state}, {$service->zip}, {$service->country}";
                }
                return '-';
            })
            ->addColumn('actions', function ($service) use ($supplier) {
                return '<a href="' . route('services.edit', [encode_id($supplier->id), encode_id($service->id)]) . '"   >
                            <i class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i>
                        </a>
                        <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                        data-supplier-id="' . encode_id($supplier->id) . '" data-service-id="' . encode_id($service->id) . '"></i>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    return view('services.index', compact('supplier'));
}


    // Show form to create a new service
    public function create($supplier_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::findOrFail($de_supplier_id);
        // Fetch origins and destinations
        $origins = Origin::all();
        $destinations = Destination::all();
        return view('services.create', compact('supplier', 'origins', 'destinations'));
    }

    // Store a newly created service
    public function store(Request $request, $supplierId)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required',
            'origin' => $request->service_type === 'freight' ? 'required|string|max:255' : 'nullable',
            'destination' => $request->service_type === 'freight' ? 'required|string|max:255' : 'nullable',
            'street' => $request->service_type === 'warehouse' ? 'required|string|max:255' : 'nullable',
            'city' => $request->service_type === 'warehouse' ? 'required|string|max:255' : 'nullable',
            'state' => $request->service_type === 'warehouse' ? 'required|string|max:255' : 'nullable',
            'zip' => $request->service_type === 'warehouse' ? 'required|string|max:20' : 'nullable',
            'country' => $request->service_type === 'warehouse' ? 'required|string|max:255' : 'nullable',
            'cost' => 'required|numeric',
            'service_name' => 'nullable',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $service = new Service($request->all());
        $service->supplier_id = $supplierId;
        $service->service_type = $request->service_type;
        $service->cost = $request->cost;
        $service->service_name = $request->service_name;
        if ($request->service_type === 'warehouse') {
            $service->street = $request->street;
            $service->city = $request->city;
            $service->state = $request->state;
            $service->zip = $request->zip;
            $service->country = $request->country;
        } else {
            $service->origin = $request->origin;
            $service->destination = $request->destination;
        }
        $service->save();

        return redirect()->route('services.index', encode_id($supplierId))->with('meassge', __('messages.Service created successfully'));
    }

    // Show form to edit the service
    public function edit($supplierId, $serviceId)
    {
        $serviceId = decode_id($serviceId);
        $de_supplier_id = decode_id($supplierId);
        $supplier = Supplier::findOrFail($de_supplier_id);
        $service = Service::with(['origindata', 'destinationdata'])->findOrFail($serviceId);
        $origins = Origin::all(); 
        $destinations = Destination::all();
        return view('services.edit', compact('service', 'supplierId', 'origins', 'destinations', 'supplier' ));
    }

    // Update the service
    public function update(Request $request, $supplierId, $serviceId)
    {

        $validator = Validator::make($request->all(), [
            'service_type' => 'required',
            'origin' => $request->service_type === 'freight' ? 'required|string|max:255' : 'nullable',
            'destination' => $request->service_type === 'freight' ? 'required|string|max:255' : 'nullable',
            'street' => $request->service_type === 'warehouse' ? 'required|string|max:255' : 'nullable',
            'city' => $request->service_type === 'warehouse' ? 'required|string|max:255' : 'nullable',
            'state' => $request->service_type === 'warehouse' ? 'required|string|max:255' : 'nullable',
            'zip' => $request->service_type === 'warehouse' ? 'required|string|max:20' : 'nullable',
            'country' => $request->service_type === 'warehouse' ? 'required|string|max:255' : 'nullable',
            'cost' => 'required|numeric',
            'service_name' => 'nullable',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service = Service::findOrFail($serviceId);
        $service->service_type = $request->service_type;
        $service->cost = $request->cost;
        $service->service_name = $request->service_name;
        // Update fields based on shipping type
        if ($request->shipping_type === 'freight') {
            $service->shipping_type = 'freight';
            $service->origin_id = $request->origin;
            $service->destination_id = $request->destination;
            // Reset warehouse fields
            $service->street = null; 
            $service->city = null;
            $service->state = null;
            $service->zip = null;
            $service->country = null;
        } elseif ($request->shipping_type === 'warehouse') {
            $service->shipping_type = 'warehouse';
            $service->origin_id = null;
            $service->destination_id = null;
            // Set warehouse details
            $service->street = $request->street;
            $service->city = $request->city;
            $service->state = $request->state;
            $service->zip = $request->zip;
            $service->country = $request->country;
        }
        $service->update($request->all());

        return redirect()->route('services.index', $supplierId)->with('message', __('messages.Service updated successfully'));
    }

    // Soft delete a service
    public function destroy($supplierId, $serviceId)
    {
        $serviceId = decode_id($serviceId);
        $service = Service::findOrFail($serviceId);
        $service->delete();

        return redirect()->route('services.index', $supplierId)->with('message', __('messages.Service deleted successfully'));
    }

}

