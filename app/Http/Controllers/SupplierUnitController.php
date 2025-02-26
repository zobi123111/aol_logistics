<?php

namespace App\Http\Controllers;

use App\Models\SupplierUnit;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierUnitController extends Controller
{
    // Display a listing of the supplier units
    public function index($supplier_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::findOrFail($de_supplier_id);
        $units = $supplier->units;  

        return view('supplier_units.index', compact('supplier', 'units'));
    }

    // Show the form for creating a new supplier unit
    public function create($supplier_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::findOrFail($de_supplier_id);
        return view('supplier_units.create', compact('supplier'));
    }

    // Store a newly created supplier unit
    public function store(Request $request, $supplierId)
    {
        $validator = Validator::make($request->all(), [
            'unit_type' => 'required|string',
            'unit_number' => 'required|string',
            // 'license_plate' => ['required','string', 'regex:/^[A-Za-z]{2}-\d{2}-[A-Za-z]{2}$/'],
            'license_plate' => 'required|string',
            'state' => 'required|string',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $supplier = Supplier::findOrFail($supplierId);
        $supplier->units()->create($request->all());

        return redirect()->route('supplier_units.index', encode_id($supplierId))->with('message', 'Equipment added successfully');
    }

    // Show the form for editing the specified supplier unit
    public function edit($supplier_id, $unitId)
    {
        $de_supplier_id = decode_id($supplier_id);
        $de_user_id = decode_id($unitId);
        $supplier = Supplier::findOrFail($de_supplier_id);
        $unit = SupplierUnit::findOrFail($de_user_id);

        return view('supplier_units.edit', compact('supplier', 'unit'));
    }

    // Update the specified supplier unit
    public function update(Request $request, $supplierId, $unitId)
    {
        $validator = Validator::make($request->all(), [
            'unit_type' => 'required|string',
            'unit_number' => 'required|string',
            // 'license_plate' => ['required','string', 'regex:/^[A-Za-z]{2}-\d{2}-[A-Za-z]{2}$/'],
            'license_plate' => 'required|string',
            'state' => 'required|string',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $unit = SupplierUnit::findOrFail($unitId);
        $unit->update($request->all());

        return redirect()->route('supplier_units.index', encode_id($supplierId))->with('message', 'Equipment updated successfully');
    }

    // Remove the specified supplier unit
    public function destroy($supplier_id, $unitId)
    {
        $de_supplier_id = decode_id($supplier_id);
        $de_user_id = decode_id($unitId);
        $unit = SupplierUnit::findOrFail($de_user_id);
        $unit->delete();

        return redirect()->route('supplier_units.index', $supplier_id)->with('message', 'Equipment deleted successfully');
    }
}

