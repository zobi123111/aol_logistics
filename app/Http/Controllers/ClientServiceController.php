<?php

namespace App\Http\Controllers;

use App\Models\ClientService; // your client services model
use App\Models\User; // the User model as client
use App\Models\MasterService;
use Illuminate\Http\Request;

class ClientServiceController extends Controller
{
    public function index(Request $request, $clientId)
    {
        $de_client_id = decode_id($clientId);
        $client = User::findOrFail($de_client_id);

        if ($request->ajax()) {
            $services = ClientService::where('client_id', $de_client_id)
                ->with(['masterService.origindata', 'masterService.destinationdata'])
                ->select('client_services.*');

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
                // ->addColumn('cost', function ($service) {
                //     return '$' . number_format($service->cost, 2);
                // })
                // ->addColumn('schedule_cost', function ($service) {
                //     return '$' . number_format($service->schedule_cost, 2);
                // })
                ->addColumn('actions', function ($service) use ($client) {
                    return '<a href="' . route('client_services.edit', [encode_id($client->id), encode_id($service->id)]) . '" class="table_icon_style blue_icon_color">
                                <i class="fa fa-edit"></i>
                            </a>
                           <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
                            data-client-id="' . encode_id($client->id) . '" data-service-id="' . encode_id($service->id) . '"></i>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('client_services.index', compact('client'));
    }

    public function create($clientId)
    {
        $de_client_id = decode_id($clientId);
        $client = User::findOrFail($de_client_id);
        $serviceTypes = MasterService::select('service_type')->distinct()->pluck('service_type');
        $masterServices = MasterService::with(['origindata', 'destinationdata'])->get()->groupBy('service_type');
        return view('client_services.create', compact('client', 'serviceTypes', 'masterServices'));
    }

    public function store(Request $request, $clientId)
    {
        // dd($clientId);
        $request->validate([
            'master_service_id' => 'required|exists:master_services,id',
            'cost' => 'required|numeric|min:0',
            'service_date' => 'nullable|date|after:today',
            'schedule_cost' => 'nullable|numeric|min:0',
            'service_type' => 'required',
        ]);

        $serviceDate = $request->service_date ? date('Y-m-d', strtotime($request->service_date)) : null;

        $existingService = ClientService::where('client_id', $clientId)
            ->where('master_service_id', $request->master_service_id)
            ->first();

        if ($existingService) {
            return redirect()->back()->withErrors([
                'master_service_id' => 'A service with the same client and master service already exists.'
            ])->withInput();
        } else {
            ClientService::create([
                'client_id' => $clientId,
                'master_service_id' => $request->master_service_id,
                'cost' => $request->cost,
                'service_date' => $serviceDate,
                'schedule_cost' => $request->schedule_cost,
            ]);
        }

        return redirect()->route('client_services.index', encode_id($clientId))->with('message', 'Client Service created successfully.');
    }

    public function edit($clientId, $clientServiceId)
    {
        $de_client_id = decode_id($clientId);
        $de_client_service_id = decode_id($clientServiceId);

        $client = User::findOrFail($de_client_id);
        $clientService = ClientService::with(['masterService.origindata', 'masterService.destinationdata'])
            ->findOrFail($de_client_service_id);

        $masterServices = MasterService::with(['origindata', 'destinationdata'])->get()->groupBy('service_type');
        $serviceTypes = $masterServices->keys();

        return view('client_services.edit', compact('client', 'clientService', 'masterServices', 'serviceTypes'));
    }

    public function update(Request $request, $clientId, $serviceId)
    {
        $request->validate([
            'master_service_id' => 'required|exists:master_services,id',
            'cost' => 'required|numeric|min:0',
            'service_date' => 'nullable|date|after:today',
            'service_type' => 'required',
        ]);

        $existingService = ClientService::where('client_id', $clientId)
            ->where('master_service_id', $request->master_service_id)
            ->where('id', '!=', $serviceId)
            ->first();

        if ($existingService) {
            return redirect()->back()->withErrors([
                'master_service_id' => 'A service with the same client and master service already exists.'
            ])->withInput();
        }

        $clientService = ClientService::where('client_id', $clientId)->findOrFail($serviceId);
        $clientService->update([
            'master_service_id' => $request->master_service_id,
            'cost' => $request->cost,
            'service_date' => $request->service_date,
            'schedule_cost' => $request->schedule_cost,
            'service_type' => $request->service_type,
        ]);

        return redirect()->route('client_services.index', encode_id($clientId))->with('message', 'Client Service updated successfully.');
    }

    public function destroy($clientId, $serviceId)
    {
        $de_client_id = decode_id($clientId);
        $de_client_service_id = decode_id($serviceId);

        $clientService = ClientService::where('client_id', $de_client_id)->findOrFail($de_client_service_id);
        $clientService->delete();

        return redirect()->route('client_services.index', $clientId)->with('message', 'Client Service deleted successfully.');
    }
}
