<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Load;
use App\Models\Supplier;
use Yajra\DataTables\DataTables;


class LoadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $loads = Load::get();
        // return view('loads.index', compact('loads'));
        if ($request->ajax()) {
            $loads = Load::get(); 
    
            return DataTables::of($loads)
            ->addColumn('actions', function ($load) {
                $editUrl = route('loads.edit', encode_id($load->id));
                $deleteId = encode_id($load->id);
            
                return '<a href="' . $editUrl . '" class="">
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
            
            ->rawColumns(['actions', 'assign']) 
            ->make(true);
        }
        return view('loads.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('loads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'payer' => 'required',
            // 'equipment_type' => 'required',
            'weight' => 'required|numeric',
            'delivery_deadline' => 'required|date',
            'customer_po' => 'required|nullable',
            'is_hazmat' => 'boolean',
            'is_inbond' => 'boolean',
        ]);
    
        Load::create(array_merge(
            $request->except('aol_number'), 
            ['status' => 'requested']
        ));    
        return redirect()->route('loads.index')->with('message', 'Load added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $en = $id;
        $de = decode_id($id);
        $load = Load::findOrFail($de);
        return view('loads.edit', compact('load'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'payer' => 'required|string',
            // 'equipment_type' => 'required|string',
            'weight' => 'required|numeric',
            'delivery_deadline' => 'required|date',
        ]);
    
        $load = Load::findOrFail($id);
        $load->update([
            'origin' => $request->origin,
            'destination' => $request->destination,
            'payer' => $request->payer,
            // 'equipment_type' => $request->equipment_type,
            'weight' => $request->weight,
            'delivery_deadline' => $request->delivery_deadline,
            'customer_po' => $request->customer_po,
            'is_hazmat' => $request->has('is_hazmat'),
            'is_inbond' => $request->has('is_inbond'),
        ]);
    
        return redirect()->route('loads.index')->with('meassge', 'Load updated successfully.');
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
    $suppliers = Supplier::whereHas('services', function ($query) use ($load) {
        $query->where('origin', $load->origin)
              ->where('destination', $load->destination);
    })->with(['services' => function ($query) use ($load) {
        $query->where('origin', $load->origin)
              ->where('destination', $load->destination)
              ->orderBy('cost', 'asc'); // Sort services by cost (ascending)
    }])->get();
    
    return view('loads.assign', compact('load', 'suppliers'));
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
    
}
