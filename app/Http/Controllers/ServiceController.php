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
    public function index($supplier_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::findOrFail($de_supplier_id);
        $services = Service::where('supplier_id', $de_supplier_id)
        ->with(['origindata', 'destinationdata'])
        ->get();
        $origins = Origin::all();
        $destinations = Destination::all();
        return view('services.index', compact('supplier', 'services', 'origins', 'destinations'));
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
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'cost' => 'required|numeric',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $service = new Service($request->all());
        $service->supplier_id = $supplierId;
        $service->save();

        return redirect()->route('services.index', encode_id($supplierId))->with('meassge', __('messages.Service created successfully'));
    }

    // Show form to edit the service
    public function edit($supplierId, $serviceId)
    {
        $serviceId = decode_id($serviceId);
        $service = Service::with(['origindata', 'destinationdata'])->findOrFail($serviceId);
        $origins = Origin::all(); 
        $destinations = Destination::all();
        return view('services.edit', compact('service', 'supplierId', 'origins', 'destinations'));
    }

    // Update the service
    public function update(Request $request, $supplierId, $serviceId)
    {

        $validator = Validator::make($request->all(), [
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'cost' => 'required|numeric',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service = Service::findOrFail($serviceId);
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

