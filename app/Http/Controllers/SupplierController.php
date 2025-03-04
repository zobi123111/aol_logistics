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
use Illuminate\Support\Facades\Storage;
use App\Models\UserActivityLog;
use App\Models\SupplierDocument;
use Yajra\DataTables\DataTables;


class SupplierController extends Controller
{
    public function index(Request $request)
    {
        // $suppliers = Supplier::all();
        // return view('suppliers.index', compact('suppliers'));
        if ($request->ajax()) {
            $suppliers = Supplier::query(); 
    
            return DataTables::of($suppliers)
            ->addColumn('status', function ($supplier) {
                return view('suppliers.partials.toggle_status', compact('supplier'))->render();
            })
            ->addColumn('actions', function ($supplier) {
                return view('suppliers.partials.actions', compact('supplier'))->render();
            })
            ->addColumn('supplier_users', function ($supplier) {
                return '<a href="'.route('supplier_users.index', encode_id($supplier->id)).'" class="btn btn-primary create-button btn_primary_color">
                            <i class="fa-solid fa-user"></i> Manage
                        </a>';
            })
            ->addColumn('supplier_units', function ($supplier) {
                return '<a href="'.route('supplier_units.index', encode_id($supplier->id)).'" class="btn btn-secondary create-button btn_secondary_color">
                            <i class="fa-solid fa-truck"></i> Manage
                        </a>';
            })
            ->addColumn('services', function ($supplier) {
                return '<a href="'.route('services.index', encode_id($supplier->id)).'" class="btn btn-primary create-button btn_primary_color">
                            <i class="fa-solid fa-gear"></i> Manage
                        </a>';
            })
            ->rawColumns(['status', 'actions', 'supplier_users', 'supplier_units', 'services']) 
            ->make(true);
        }
    
        return view('suppliers.index');
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        // Define validation supplierdata
        $supplierdata = [
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
            // 'user_role' => 'required',
            'user_email' => 'required|email|max:255',
            'user_office_phone' => 'required|string|max:20',
            'user_mobile_phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'service_type' => 'required',
            'currency' => 'required',
            'preferred_language' => 'required',
            'document_path' => 'array',
            'document_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'scac_number' => 'required|string|max:50',
            'scac_documents' => 'array',
            'scac_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'caat_number' => 'required|string|max:50',
            'caat_documents' => 'array',
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
        $validator = Validator::make($request->all(), $supplierdata, $messages);
    
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
            // $role = Role::where('role_slug', $request->user_role)->first();
            $role = Role::where('role_slug', config('constants.roles.MASTERCLIENT'))->first();

            
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
                'user_id' => $user->id,
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
                'is_supplier' => true,
                // 'documents' => $request->hasFile('document_path') 
                // ? collect($request->file('document_path'))->map(function ($file) {
                //     return $file->store('documents' , 'public');
                // })->toJson()
                // : null,
                'scac_number' => $request->scac_number,
                // 'scac_documents' => $request->hasFile('scac_documents') 
                // ? collect($request->file('scac_documents'))->map(function ($file) {
                //     return $file->store('scac_documents', 'public');
                // })->toJson()
                // : null,
                'caat_number' => $request->caat_number,
                // 'caat_documents' => $request->hasFile('caat_documents') 
                // ? collect($request->file('caat_documents'))->map(function ($file) {
                //     return $file->store('caat_documents', 'public');
                // })->toJson()
                // : null,
            ]);

            // add log
            UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_CREATE_SUPPLIER,
            'description' => 'A new master supplier'. ' (' .$request->user_email . ') has been created by ' 
                    . auth()->user()->fname . ' ' 
                    . auth()->user()->lname 
                    . ' (' . auth()->user()->email . ')',
                'user_id' => auth()->id(), 
            ]);

            // Function to save documents
            $this->storeDocuments($supplier->id, $request, 'document_path', 'documents');
            $this->storeDocuments($supplier->id, $request, 'scac_documents', 'scac_documents');
            $this->storeDocuments($supplier->id, $request, 'caat_documents', 'caat_documents');
        
            DB::commit();
            return redirect()->route('suppliers.index')
            ->with('message', __('messages.Supplier created successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
            ->withInput()
            ->withErrors(['message' => __('messages.Supplier creation failed! Please try again later.')]);
                }
        
        // Redirect with success message
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully!');
    }

    public function show($supplier_id)
    {
        $en = $supplier_id;
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::with('supplierdocuments')->find($de_supplier_id);
        if (!$supplier) {
            return redirect()->route('suppliers.index')->with('error', 'Supplier not found.');
        }
        return view('suppliers.show', compact('supplier'));
    }

    public function edit($supplier_id)
    {
        $en = $supplier_id;
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::with(['user', 'supplierdocuments'])->findOrFail($de_supplier_id);
        // dd($supplier, $supplier->supplierdocuments);
        return view('suppliers.edit', compact('supplier')); 
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $supplierdata = [
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
            // 'user_role' => 'required',
            'user_email' => 'required|email|max:255',
            'user_office_phone' => 'required|string|max:20',
            'user_mobile_phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'service_type' => 'required',
            'currency' => 'required',
            'preferred_language' => 'required',
            'document_path' => 'array',
            'document_path.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'scac_number' => 'required|string|max:50',
            'scac_documents' => 'array',
            'scac_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'caat_number' => 'required|string|max:50',
            'caat_documents' => 'array',
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
        $validator = Validator::make($request->all(), $supplierdata, $messages);
        if ($validator->fails()) {
            // Redirect back with errors and input values
        // dd("fdjghkf");

            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $this->storeDocuments($supplier->id, $request, 'document_path', 'documents');
        $this->storeDocuments($supplier->id, $request, 'scac_documents', 'scac_documents');
        $this->storeDocuments($supplier->id, $request, 'caat_documents', 'caat_documents');
        // // Documents Handling
        // $existingDocuments = json_decode($supplier->documents, true) ?? [];
        // $existingScacDocuments = json_decode($supplier->scac_documents, true) ?? [];
        // $existingCaatDocuments = json_decode($supplier->caat_documents, true) ?? [];

        // // Handle document deletion
        // if ($request->has('delete_documents')) {
        //     $remainingDocuments = array_diff($existingDocuments, $request->delete_documents);
        //     if (empty($remainingDocuments) && !$request->hasFile('document_path')) {
        //         return redirect()->back()->withErrors(['document_path' => 'At least one document must be uploaded.']);
        //     }
        //     foreach ($request->delete_documents as $deletedFile) {
        //         Storage::disk('public')->delete($deletedFile);
        //     }
        //     $existingDocuments = $remainingDocuments;
        // }

        // // Handle scac_documents deletion
        // if ($request->has('delete_scac_documents')) {
        //     $remainingScacDocuments = array_diff($existingScacDocuments, $request->delete_scac_documents);
        //     if (empty($remainingScacDocuments) && !$request->hasFile('scac_documents')) {
        //         return redirect()->back()->withErrors(['scac_documents' => 'At least one SCAC document must be uploaded.']);
        //     }
        //     foreach ($request->delete_scac_documents as $deletedFile) {
        //         Storage::disk('public')->delete($deletedFile);
        //     }
        //     $existingScacDocuments = $remainingScacDocuments;
        // }

        // // Handle caat_documents deletion
        // if ($request->has('delete_caat_documents')) {
        //     $remainingCaatDocuments = array_diff($existingCaatDocuments, $request->delete_caat_documents);
        //     if (empty($remainingCaatDocuments) && !$request->hasFile('caat_documents')) {
        //         return redirect()->back()->withErrors(['caat_documents' => 'At least one CAAT document must be uploaded.']);
        //     }
        //     foreach ($request->delete_caat_documents as $deletedFile) {
        //         Storage::disk('public')->delete($deletedFile);
        //     }
        //     $existingCaatDocuments = $remainingCaatDocuments;
        // }

        // // Handle document upload
        // if ($request->hasFile('document_path')) {
        //     $newDocuments = collect($request->file('document_path'))->map(function ($file) {
        //         return $file->store('documents', 'public');
        //     })->toArray();
        //     $existingDocuments = array_merge($existingDocuments, $newDocuments);
        // }

        // // Handle scac_document upload
        // if ($request->hasFile('scac_documents')) {
        //     $newScacDocuments = collect($request->file('scac_documents'))->map(function ($file) {
        //         return $file->store('scac_documents', 'public');
        //     })->toArray();
        //     $existingScacDocuments = array_merge($existingScacDocuments, $newScacDocuments);
        // }

        // // Handle caat_document upload
        // if ($request->hasFile('caat_documents')) {
        //     $newCaatDocuments = collect($request->file('caat_documents'))->map(function ($file) {
        //         return $file->store('caat_documents', 'public');
        //     })->toArray();
        //     $existingCaatDocuments = array_merge($existingCaatDocuments, $newCaatDocuments);
        // }

        $user = $supplier->user; // Assuming Supplier has a `user` relation

        // If the user exists, update their information
        if ($user) {
            if (User::where('email', $request->user_email)->where('id', '!=', $user->id)->exists()) {
                return redirect()->back()->withInput()->withErrors(['user_email' => 'The email is already registered.']);
            }

            // Update the user's email
            $user->email = $request->user_email;

            // Update the user's password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Update the user's role
            // $role = Role::where('role_slug', $request->user_role)->first();
            $role = Role::where('role_slug', config('constants.roles.MASTERCLIENT'))->first();

            if ($role) {
                $user->role = $role->id;
            }

            // Save the user updates
            $user->save();
        }

         // Check for documents to delete
        if ($request->has('delete_documents')) {
            foreach ($request->delete_documents as $filePath) {
                // Delete the file from storage
                if (Storage::exists('public/' . $filePath)) {
                    Storage::delete('public/' . $filePath);
                }

                // Delete the corresponding record from supplier_documents table
                SupplierDocument::where('supplier_id', $id)
                    ->where('file_path', $filePath)
                    ->delete();
            }
        }
        // Update Supplier
        $supplier->update([
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
            // 'user_role' => $request->user_role,
            'user_email' => $request->user_email,
            'user_office_phone' => $request->user_office_phone,
            'user_mobile_phone' => $request->user_mobile_phone,
            'service_type' => $request->service_type,
            'currency' => $request->currency,
            'preferred_language' => $request->preferred_language,
            'scac_number' => $request->scac_number,
            'caat_number' => $request->caat_number,
            // 'documents' => json_encode($existingDocuments),
            // 'scac_documents' => json_encode($existingScacDocuments),
            // 'caat_documents' => json_encode($existingCaatDocuments),
        ]);
        // add log
        UserActivityLog::create([
        'log_type' => UserActivityLog::LOG_TYPE_EDIT_SUPPLIER,
        'description' => 'A supplier'. ' (' .$request->user_email . ') has been updated by ' 
                . auth()->user()->fname . ' ' 
                . auth()->user()->lname 
                . ' (' . auth()->user()->email . ')',
            'user_id' => auth()->id(), 
        ]);

        return redirect()->route('suppliers.edit', encode_id($supplier->id))
        ->with('message', __('messages.Supplier updated successfully!'));
    }


    public function destroy($supplier_id)
    {
        $de_supplier_id = decode_id($supplier_id);
        $supplier = Supplier::find($de_supplier_id);
        if (!$supplier) {
            return redirect()->route('suppliers.index')->with('error', 'Supplier not found.');
        }
        $supplier->delete();

        // add log
        UserActivityLog::create([
        'log_type' => UserActivityLog::LOG_TYPE_DELETE_SUPPLIER,
        'description' => 'A supplier'. ' (' .$supplier->user_email . ') has been deleted by ' 
                . auth()->user()->fname . ' ' 
                . auth()->user()->lname 
                . ' (' . auth()->user()->email . ')',
            'user_id' => auth()->id(), 
        ]);
        Session::flash('message', __('messages.Supplier deleted successfully.'));
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

        private function handleDocuments($supplier, $request, $documentField, $documentFileField, $deleteDocumentField, $storageFolder)
    {
        // Retrieve existing documents
        $existingDocuments = json_decode($supplier->$documentField, true) ?? [];

        // Handle document deletion
        if ($request->has($deleteDocumentField)) {
            $remainingDocuments = array_diff($existingDocuments, $request->$deleteDocumentField);
            if (empty($remainingDocuments) && !$request->hasFile($documentFileField)) {
                return redirect()->back()->withErrors([$documentFileField => 'At least one document must be uploaded.']);
            }
            foreach ($request->$deleteDocumentField as $deletedFile) {
                Storage::disk('public')->delete($deletedFile);
            }
            $existingDocuments = $remainingDocuments;
        }

        // Handle document upload
        if ($request->hasFile($documentFileField)) {
            $newDocuments = collect($request->file($documentFileField))->map(function ($file) use ($storageFolder) {
                return $file->store($storageFolder, 'public');
            })->toArray();
            $existingDocuments = array_merge($existingDocuments, $newDocuments);
        }

        // Save the updated documents back to the supplier
        $supplier->$documentField = json_encode($existingDocuments);
    }

    public function toggleStatus(Request $request)
    {
        $supplierId = decode_id($request->user_id);
        $isActive = $request->is_active;
        $supplier = Supplier::findOrFail($supplierId);

        // Store the old status before updating
        $oldStatus = $supplier->is_active ? 'Active' : 'Inactive';
        $newStatus = $isActive ? 'Active' : 'Inactive';

        $user = User::findOrFail($supplier->user_id);
        $user->is_active = $isActive;
        $user->save();

        // Update the user's status
        $supplier->is_active = $isActive;
        $supplier->save();

        // Log the status update
        UserActivityLog::create([
            'log_type' => UserActivityLog::LOG_TYPE_UPDATE_STATUS_SUPPLIER,
            'description' => 'Supplier  (' . $supplier->user_email . ') status changed from ' 
                            . $oldStatus . ' to ' . $newStatus . ' by User with email (' . auth()->user()->email . ')',
            'user_id' => auth()->id(),
        ]);
        return response()->json([
            'success' => true,
            'message' => __('messages.Supplier status updated successfully'),
            'is_active' => $supplier->is_active
        ]);
    }


    private function storeDocuments($supplierId, $request, $inputName, $documentType) {
        if ($request->hasFile($inputName)) {
            foreach ($request->file($inputName) as $file) {
                $filePath = $file->store($documentType, 'public');
    
                SupplierDocument::create([
                    'supplier_id' => $supplierId,
                    'document_type' => $documentType,
                    'file_path' => $filePath,
                ]);
            }
        }
    }
}