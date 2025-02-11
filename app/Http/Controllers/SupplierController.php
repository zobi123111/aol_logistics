<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Session;


class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'company_name' => 'required|string|max:255',
            'dba' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|regex:/^\d{5}$/',
            'country' => 'required|string|max:100',
            'office_phone' => 'required|string|max:20',
            'primary_contact_email' => 'required|email|max:255',
            'primary_contact_office_phone' => 'required|string|max:20',
            'primary_contact_mobile_phone' => 'required|string|max:20',
            'user_role' => 'required',
            'user_email' => 'required|email|max:255',
            'user_office_phone' => 'required|string|max:20',
            'user_mobile_phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'service_type' => 'required',
            'currency' => 'required',
            'preferred_language' => 'required',
            'document_path' => 'required|array',
            'document_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'scac_number' => 'required|string|max:50',
            'scac_documents' => 'required|array',
            'scac_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'caat_number' => 'required|string|max:50',
            'caat_documents' => 'required|array',
            'caat_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    
        $messages = [
            'document_path.*.mimes' => 'The document must be a PDF, JPG, JPEG, or PNG image.',
            'document_path.*.max' => 'Each document must be smaller than 2MB.',
            'document_path.required' => 'Please upload at least one document.',
            'scac_documents.*.mimes' => 'The document must be a PDF, JPG, JPEG, or PNG image.',
            'scac_documents.*.max' => 'Each document must be smaller than 2MB.',
            'scac_documents.required' => 'Please upload at least one document.',
            'caat_documents.*.mimes' => 'The document must be a PDF, JPG, JPEG, or PNG image.',
            'caat_documents.*.max' => 'Each document must be smaller than 2MB.',
            'caat_documents.required' => 'Please upload at least one document.',
        ];
        // Run the validation
        $validator = Validator::make($request->all(), $rules, $messages);
    
        // Check for validation errors
        if ($validator->fails()) {
            // Redirect back with errors and input values
            return redirect()->back()->withErrors($validator)->withInput();
        }
        DB::beginTransaction();
        try {
            // Check if email already exists
            if (User::where('email', $request->user_email)->exists()) {
                return redirect()->back()->withInput()->withErrors(['user_email' => 'The email is already registered.']);

            }
             
            // Find role ID by matching role_slug with user_role
            $role = Role::where('role_slug', $request->user_role)->first();
            
            if (!$role) {
                return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
            }

            // Create a new user
            $user = User::create([
                'email' => $request->user_email,
                'password' => Hash::make($request->password),
                'role' => $role->id,
                'created_by' => auth()->id(),
            ]);

        
            $supplier = Supplier::create([
                'company_name' => $request->company_name,
                'dba' => $request->dba,
                'street_address' => $request->street_address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'office_phone' => $request->office_phone,
                'primary_contact_email' => $request->primary_contact_email,
                'primary_contact_office_phone' => $request->primary_contact_office_phone,
                'primary_contact_mobile_phone' => $request->primary_contact_mobile_phone,
                'user_role' => $request->user_role,
                'user_email' => $request->user_email,
                'user_office_phone' => $request->user_office_phone,
                'user_mobile_phone' => $request->user_mobile_phone,
                'service_type' => $request->service_type,
                'currency' => $request->currency,
                'preferred_language' => $request->preferred_language,
                'documents' => $request->hasFile('document_path') 
                ? collect($request->file('document_path'))->map(function ($file) {
                    return $file->store('documents' , 'public');
                })->toJson()
                : null,
                'scac_number' => $request->scac_number,
                            'scac_documents' => $request->hasFile('scac_documents') 
                ? collect($request->file('scac_documents'))->map(function ($file) {
                    return $file->store('scac_documents', 'public');
                })->toJson()
                : null,
                'caat_number' => $request->caat_number,
                'caat_documents' => $request->hasFile('caat_documents') 
                ? collect($request->file('caat_documents'))->map(function ($file) {
                    return $file->store('caat_documents', 'public');
                })->toJson()
                : null,
            ]);
        
            DB::commit();
            return redirect()->route('suppliers.index')
            ->with('message', 'Supplier created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
            ->withInput()
            ->withErrors(['message' => 'Supplier creation failed! Please try again later.']);
                }
        
        // Redirect with success message
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully!');
    }

    public function show($supplier_id)
    {
        $en = $supplier_id;
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::find($de_supplier_id);
        if (!$supplier) {
            return redirect()->route('suppliers.index')->with('error', 'Supplier not found.');
        }
        return view('suppliers.show', compact('supplier'));
    }


    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'office_phone' => 'required|string',
            'primary_contact_email' => 'required|email',
            'service_type' => 'required|string',
            'currency' => 'required|string',
            'preferred_language' => 'required|string',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($supplier_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::find($de_supplier_id);
        if (!$supplier) {
            return redirect()->route('suppliers.index')->with('error', 'Supplier not found.');
        }
        $supplier->delete();
        Session::flash('message', 'Supplier deleted successfully.');
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}