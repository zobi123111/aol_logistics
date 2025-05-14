<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\MasterService;
use App\Models\Origin;
use App\Models\Destination;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;


class MasterServiceController extends Controller
{

    public function index(Request $request)
{
    if ($request->ajax()) {
        // Query to fetch master services
        // $services = MasterService::query();
        $services = MasterService::with(['origindata', 'destinationdata'])
        ->select();
        return DataTables::of($services)
            // ->addColumn('status', function ($service) {
            //     // Add custom toggle status column, or you can customize it
            //     return view('master-services.partials.toggle_status', compact('service'))->render();
            // })
            ->addColumn('actions', function ($service) {
                $editUrl = route('master-services.edit', encode_id($service->id));
                $deleteId = encode_id($service->id);
            
                return '
                   
                    <a href="' . $editUrl . '" class="table_icon_style blue_icon_color">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="#" class="delete-icon table_icon_style red_icon_color" data-service-id="' . $deleteId . '">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                ';
            })
            ->addColumn('origin', function ($service) {
                return $service->service_type !== 'warehouse' && $service->origindata
                ? ($service->origindata->name ?: ($service->origindata->street . ', ' . $service->origindata->city . ', ' . $service->origindata->state . ', ' . $service->origindata->country))
                : '-';
            })
            ->addColumn('destination', function ($service) {
                return $service->service_type !== 'warehouse' && $service->destinationdata
                ? ($service->destinationdata->name ?: ($service->destinationdata->street . ', ' . $service->destinationdata->city . ', ' . $service->destinationdata->state . ', ' . $service->destinationdata->country))
                : '-';
            })
            ->addColumn('warehouse', function ($service) {
                if ($service->service_type === 'warehouse') {
                    return "{$service->street}, {$service->city}, {$service->state}, {$service->zip}, {$service->country}";
                }
                return '-';
            })
            ->rawColumns(['status', 'actions'])  // Make the columns render raw HTML
            ->make(true);
    }

    return view('master-services.index');
}


    // Show form to create a new service
    public function create()
    {
        $origins = Origin::all();
        $destinations = Destination::all();
        return view('master-services.create', compact( 'origins', 'destinations'));
    }

    // Store a newly created service
    public function store(Request $request)
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
            'service_name' => 'nullable',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $service = new MasterService($request->all());
        $service->service_type = $request->service_type;
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

        return redirect()->route('master-services.index')->with('meassge', __('messages.Service created successfully'));
    }

    // Show form to edit the service
    public function edit( $serviceId)
    {
        $serviceId = decode_id($serviceId);
        $service = MasterService::with(['origindata', 'destinationdata'])->findOrFail($serviceId);
        // dd($service);
        $origins = Origin::all(); 
        $destinations = Destination::all();
        return view('master-services.edit', compact('service', 'origins', 'destinations' ));
    }

    // Update the service
    public function update(Request $request, $serviceId)
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
            'service_name' => 'nullable',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service = MasterService::findOrFail($serviceId);
        $service->service_type = $request->service_type;
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

        return redirect()->route('master-services.index')->with('message', __('messages.Service updated successfully'));
    }

    // Soft delete a service
    public function destroy( $serviceId)
    {
        $serviceId = decode_id($serviceId);
        $service = MasterService::findOrFail($serviceId);
        $service->delete();

        return redirect()->route('master-services.index')->with('message', __('messages.Service deleted successfully'));
    }

}

