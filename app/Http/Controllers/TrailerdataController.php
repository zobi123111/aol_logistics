<?php
namespace App\Http\Controllers;

use App\Models\Trailerdata;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class TrailerdataController extends Controller
{


    public function index(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail(decode_id($supplierId));

        if ($request->ajax()) {
                $trailers = Trailerdata::where('supplier_id', $supplier->id);
                    return DataTables::of($trailers)
                        ->addColumn('actions', function ($trailer) {
                        $editUrl = route('supplier_trailers.edit', [
                    'supplierId' => encode_id($trailer->supplier_id),
                    'trailer' => encode_id($trailer->id)
                ]);

                $deleteId =  encode_id($trailer->id);
                $supId =  encode_id($trailer->supplier_id);

                return '
                    <a href="' . $editUrl . '" class="">
                        <i class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i>
                    </a>
                    <a href="#" class="delete-icon table_icon_style blue_icon_color" data-trailer-id="' . $deleteId . '" data-sup-id="' . $supId . '">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                ';
                })
                ->rawColumns(['actions']) // allow HTML to render
                ->make(true);
        }

        return view('supplier_trailers.index', compact('supplier'));
    }
    public function create($supplierId)
    {
        $decodedId = decode_id($supplierId); // If applicable
        $supplier = Supplier::findOrFail($decodedId);

        return view('supplier_trailers.create', compact('supplier'));
    }

        public function store(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail(decode_id($supplierId));

        $validated = $request->validate([
            'trailer_num' => 'required|numeric',
        ]);

        $validated['supplier_id'] = $supplier->id;

        Trailerdata::create($validated);

        return redirect()
            ->route('supplier_trailers.index', encode_id($supplier->id))
            ->with('message', 'Trailer added successfully.');
    }

    public function edit($supplierId, $trailerId)
    {
        // Decode IDs (if you're encoding them in URLs)
        $supplierId = decode_id($supplierId);
        $trailerId = decode_id($trailerId);

        // Retrieve the supplier and trailer
        $supplier = Supplier::findOrFail($supplierId);
        $trailer = Trailerdata::where('supplier_id', $supplierId)->findOrFail($trailerId);

        // Return the edit view with data
        return view('supplier_trailers.edit', compact('supplier', 'trailer'));
    }

   public function update(Request $request, $supplierId, $trailerId)
    {
        $supplierId = decode_id($supplierId);
        $trailerId = decode_id($trailerId);

        $validatedData = $request->validate([
            'trailer_num' => 'required|numeric',
        ]);

        $trailer = Trailerdata::where('supplier_id', $supplierId)->findOrFail($trailerId);

        $trailer->trailer_num = $validatedData['trailer_num'];
        $trailer->save();

        return redirect()
            ->route('supplier_trailers.index', encode_id($supplierId))
            ->with('message', __('messages.Updated Successfully'));
    }

    public function destroy($supplierId, $trailerId)
    {
        $supplierId = decode_id($supplierId);
        $trailerId = decode_id($trailerId); 

        $trailer = Trailerdata::where('supplier_id', $supplierId)->where('id', $trailerId)->first();

        if (!$trailer) {
            return redirect()->back()->with('error', __('messages.Truck not found.'));
        }

        $trailer->delete();

        return redirect()->route('supplier_trailers.index', encode_id($supplierId))
                ->with('message', __('messages.Truck deleted successfully.'));

    }

}

