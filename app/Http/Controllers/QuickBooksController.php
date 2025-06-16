<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2AccessToken;
use QuickBooksOnline\API\Data\IPPAttachable;
use QuickBooksOnline\API\Data\IPPAttachableRef;
use QuickBooksOnline\API\Data\IPPReferenceType;
use QuickBooksOnline\API\Facades\Customer;
use App\Models\Load;
use QuickBooksOnline\API\Facades\Item;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\Facades\Bill;
use QuickBooksOnline\API\Facades\Vendor;
use Illuminate\Support\Facades\Session;
use App\Models\AssignedService;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Illuminate\Support\Facades\Storage;
use App\Models\QuickbooksToken;
use Illuminate\Support\Facades\Schema;


class QuickBooksController extends Controller
{
       public function __construct()
    {
          if (Schema::hasTable('quickbooks_tokens')) {
        // Check if the refresh token exists in cache
        if (!Cache::has('qb_refresh_token')) {
            $tokenRow = QuickbooksToken::first();
            if ($tokenRow && $tokenRow->refresh_token) {
                Cache::put('qb_refresh_token', $tokenRow->refresh_token, now()->addDays(90));
            }
        }

    if (!Cache::has('qb_access_token')) {
        $tokenRow = $tokenRow ?? QuickbooksToken::first(); // reuse if already fetched above

        if ($tokenRow && $tokenRow->access_token && $tokenRow->access_token_expires_at) {
            Cache::put('qb_access_token', $tokenRow->access_token, \Carbon\Carbon::parse($tokenRow->access_token_expires_at));
        }
    }
    }
    }

    // ✅ Function to refresh QuickBooks Access Token
    public function refreshAccessToken()
    {
        $refreshToken = Cache::get('qb_refresh_token');

        if (!$refreshToken) {
            return response()->json(['error' => 'QuickBooks Refresh Token is missing.'], 401);
        }

        try {
            // Configure DataService for token refresh
            $dataService = DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientID' => env('QB_CLIENT_ID'),
                'ClientSecret' => env('QB_CLIENT_SECRET'),
                'RedirectURI' => env('QB_REDIRECT_URI'),
                'refreshTokenKey' => $refreshToken,
                'QBORealmID' => env('QB_REALM_ID'),
                'baseUrl' => env('QB_ENV') === 'production' ? "production" : "sandbox"
            ]);

            // Get new access token
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $newAccessToken = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($refreshToken);

            // Store new tokens
            Cache::put('qb_access_token', $newAccessToken->getAccessToken(), now()->addSeconds($newAccessToken->getAccessTokenExpiresAt()));
            Cache::put('qb_refresh_token', $newAccessToken->getRefreshToken(), now()->addDays(90));

                $this->updateQuickBooksTokensInDB(
                    $newAccessToken->getAccessToken(),
                    $newAccessToken->getRefreshToken(),
                    now()->addSeconds($newAccessToken->getAccessTokenExpiresAt())
                );
            return $newAccessToken->getAccessToken();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to refresh QuickBooks Access Token: ' . $e->getMessage()], 500);
        }
    }

    // ✅ Function to create an invoice
    public function createInvoice()
    {
        // Retrieve stored access token
        $accessToken = Cache::get('qb_access_token');

        // Refresh token if missing
        if (!$accessToken) {
            $accessToken = $this->refreshAccessToken();
        }

        try {
            // Configure QuickBooks DataService
            $dataService = DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientID' => env('QB_CLIENT_ID'),
                'ClientSecret' => env('QB_CLIENT_SECRET'),
                'RedirectURI' => env('QB_REDIRECT_URI'),
                'accessTokenKey' => $accessToken,
                'refreshTokenKey' => Cache::get('qb_refresh_token'),
                'QBORealmID' => env('QB_REALM_ID'),
                'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com"

            ]);

            $dataService->setLogLocation(storage_path('logs/quickbooks.log'));
            $dataService->throwExceptionOnError(true);

            // ✅ Create invoice data
            $invoiceData = [
                "Line" => [
                    [
                        "DetailType" => "SalesItemLineDetail",
                        "Amount" => 100.00,
                        "SalesItemLineDetail" => [
                            "ItemRef" => [
                                "value" => "1",
                                "name" => "Test Product"
                            ]
                        ]
                    ]
                ],
                "CustomerRef" => [
                    "value" => "2"
                ]
            ];

            $invoice = Invoice::create($invoiceData);

            // ✅ Send invoice to QuickBooks
            $result = $dataService->Add($invoice);

            if (!$result) {
                $error = $dataService->getLastError();
                return response()->json(['error' => $error->getResponseBody()], 500);
            }

            return response()->json(['success' => 'Invoice added successfully!', 'InvoiceID' => $result->Id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'QuickBooks Invoice Creation Failed: ' . $e->getMessage()], 500);
        }
    }

    // ✅ Function to Connect QuickBooks (Get Authorization Code)
    public function connect()
    {
        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => env('QB_CLIENT_ID'),
            'ClientSecret' => env('QB_CLIENT_SECRET'),
            'RedirectURI' => env('QB_REDIRECT_URI'),
            'scope' => "com.intuit.quickbooks.accounting",
            'baseUrl' => env('QB_ENV') === 'production' ? "production" : "sandbox"
        ]);

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

        return redirect()->away($authUrl);
    }

    // ✅ Callback after QuickBooks authentication
    public function callback(Request $request)
    {
        $code = $request->get('code');
        $realmId = $request->get('realmId');

        if (!$code || !$realmId) {
            return response()->json(['error' => 'Missing authorization code or realm ID'], 400);
        }

        try {
            // Get new tokens
            $dataService = DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientID' => env('QB_CLIENT_ID'),
                'ClientSecret' => env('QB_CLIENT_SECRET'),
                'RedirectURI' => env('QB_REDIRECT_URI'),
                'QBORealmID' => $realmId,
                'baseUrl' => env('QB_ENV') === 'production' ? "production" : "sandbox"
            ]);

            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, env('QB_REDIRECT_URI'));

            // Store tokens
            Cache::put('qb_access_token', $accessToken->getAccessToken(), now()->addSeconds($accessToken->getAccessTokenExpiresAt()));
            Cache::put('qb_refresh_token', $accessToken->getRefreshToken(), now()->addDays(90));
                $this->updateQuickBooksTokensInDB(
                    $accessToken->getAccessToken(),
                    $accessToken->getRefreshToken(),
                    now()->addSeconds($accessToken->getAccessTokenExpiresAt())
                );
            return response()->json(['success' => 'QuickBooks connected successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'QuickBooks connection failed: ' . $e->getMessage()], 500);
        }
    }


    public function storeInvoice(Request $request)
    {
        if (!$request->hasFile('invoice_pdf')) {
            return response()->json(['error' => 'No file uploaded.'], 400);
        }
        $request->validate([
            'invoice_number' => 'required|string',
            'invoice_pdf' => 'required|mimes:pdf|max:2048',
        ]);

        // Save the uploaded file
        // $path = $request->file('invoice_pdf')->store('invoices');
        $file = $request->file('invoice_pdf');
        $filePath = $file->store('invoices', 'public'); // Save file in Laravel storage
        $fileName = $file->getClientOriginalName();
        $fileContent = file_get_contents(storage_path("app/public/" . $filePath));

        // Get QuickBooks Access Token
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return back()->withErrors(['error' => 'QuickBooks authentication failed.']);
        }

        // Initialize QuickBooks DataService
        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => env('QB_CLIENT_ID'),
            'ClientSecret' => env('QB_CLIENT_SECRET'),
            'RedirectURI' => env('QB_REDIRECT_URI'),
            'accessTokenKey' => $accessToken,
            'refreshTokenKey' => Cache::get('qb_refresh_token'),
            'QBORealmID' => env('QB_REALM_ID'),
            'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com"
        ]);

        $dataService->setLogLocation(storage_path('logs/quickbooks.log'));
        $query = "SELECT * FROM Customer WHERE DisplayName = 'simran1234'";
        $existingCustomers = $dataService->Query($query);

        if ($existingCustomers && count($existingCustomers) > 0) {
            $customer = $existingCustomers[0];
        } else {
            $customerData = Customer::create([
                "GivenName" => 'Supplier First Name',
                "FamilyName" => 'Supplier Last Name',
                "CompanyName" => 'Supplier Company',
                "DisplayName" => 'simran1234',
                "PrimaryPhone" => ["FreeFormNumber" => "1234567898"],
                "PrimaryEmailAddr" => ["Address" => "simran@1234gmail.com"]
            ]);

            $customer = $dataService->Add($customerData);
        }
        $customerId = $customer->Id;
        // Create Invoice Object
        $invoiceData = Invoice::create([
            "DocNumber" => $request->invoice_number,
            "Line" => [[
                "Amount" => 870.00, // Replace with actual amount
                "DetailType" => "SalesItemLineDetail",
                "SalesItemLineDetail" => [
                    "ItemRef" => ["value" => "1", "name" => "Test Item"]
                ]
            ]],
            "CustomerRef" => ["value" => "1"] // Replace with actual customer ID
        ]);

        // Send Invoice to QuickBooks
        $resultingInvoice = $dataService->Add($invoiceData);
        $invoiceId = $resultingInvoice->Id;
        $randId = rand(); // Generate a random ID for the file name
        $fileName = $randId . ".pdf"; // PDF file name
        $sendMimeType = "application/pdf"; // MIME Type for PDF

        // Reference to the Invoice (Ensure $invoiceObj->Id is valid)
        $entityRef = new IPPReferenceType([
            'value' => $invoiceId,
            'type'  => 'Invoice'
        ]);

        $attachableRef = new IPPAttachableRef([
            'EntityRef' => $entityRef
        ]);

        $objAttachable = new IPPAttachable();
        $objAttachable->FileName = $fileName;
        $objAttachable->AttachableRef[] = $attachableRef;
        $objAttachable->Category = 'Attachment';
        $objAttachable->Tag = 'Invoice_PDF_' . $randId;

        // Upload the PDF attachment to QuickBooks Invoice
        $response = $dataService->Upload(
            $fileContent,     // Binary PDF content
            $fileName,    // File Name
            $sendMimeType, // MIME Type (application/pdf)
            $objAttachable // Attachable Object
        );

        dd($resultingInvoice);
        // Debug response
        // dd($response);
        if (!$resultingInvoice) {
            return back()->withErrors(['error' => 'QuickBooks Invoice Creation Failed: ' . $dataService->getLastError()]);
        }

        return redirect()->route('invoice.upload', $request->load_id)->with('success', 'Invoice uploaded successfully!');
    }


    private function getAccessToken()
    {
        $accessToken = Cache::get('qb_access_token');

        if (!$accessToken) {
            // Refresh token logic
            $refreshToken = Cache::get('qb_refresh_token');
            if (!$refreshToken) {
                return null;
            }

            // Configure QuickBooks DataService
            $dataService = DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientID' => env('QB_CLIENT_ID'),
                'ClientSecret' => env('QB_CLIENT_SECRET'),
                'RedirectURI' => env('QB_REDIRECT_URI'),
                'scope' => "com.intuit.quickbooks.accounting",
                'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com",
                'QBORealmID' => env('QB_REALM_ID'),
                'accessTokenKey' => null,
                'refreshTokenKey' => $refreshToken
            ]);

            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

            // Refresh access token
            $accessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($refreshToken);

            if (!$accessTokenObj) {
                return null;
            }

            Cache::put('qb_access_token', $accessTokenObj->getAccessToken(), now()->addSeconds($accessTokenObj->getAccessTokenExpiresAt()));
            Cache::put('qb_refresh_token', $accessTokenObj->getRefreshToken(), now()->addDays(90));
                $this->updateQuickBooksTokensInDB(  
                    $accessTokenObj->getAccessToken(),
                    $accessTokenObj->getRefreshToken(),
                    now()->addSeconds($accessTokenObj->getAccessTokenExpiresAt())
                );
            return $accessTokenObj->getAccessToken();
        }

        return $accessToken;
    }

    public function showUploadForm($id)
    {
        // $ssssss = $this->getInvoices();
        return view('invoices.upload', ['loadId' => $id]);
    }

    public function getInvoices()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return response()->json(['error' => 'QuickBooks Access Token is missing.'], 401);
        }

        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => env('QB_CLIENT_ID'),
            'ClientSecret' => env('QB_CLIENT_SECRET'),
            'RedirectURI' => env('QB_REDIRECT_URI'),
            'scope' => "com.intuit.quickbooks.accounting",
            'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com",

            'QBORealmID' => env('QB_REALM_ID'),
            'accessTokenKey' => $accessToken,
            'refreshTokenKey' => Cache::get('qb_refresh_token'),
        ]);

        // $dataService->setLogLevel(3);
        $dataService->throwExceptionOnError(true);

        // Fetch all invoices
        $invoices = $dataService->Query("SELECT * FROM Attachable");
        // $invoiceAttachments = [];

        // $query = "SELECT * FROM Invoice WHERE DocNumber = '103'";
        // $invoices = $dataService->Query($query);
        dd($invoices);

        if (!$invoices) {
            $error = $dataService->getLastError();
            return response()->json(['error' => 'QuickBooks Invoice Fetch Failed', 'details' => $error ? $error->getResponseBody() : 'Unknown Error'], 500);
        }

        return response()->json($invoices);
    }


    public function addClientInvoice($load_id)
    {
        try {
            $en = $load_id;
            $de = decode_id($load_id);
            $de = $en;
            $load = Load::with(['creatorfor', 'assignedServices.service'])->findOrFail($de);
            if (!$load) {
                Log::error("Load not found for ID: $de");
                return null;
            }
            $businessName = optional($load->creatorfor)->business_name ?? optional($load->creatorfor)->email;
            // $email = $load->creatorfor->email;

            // Get QuickBooks Access Token
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error('QuickBooks authentication failed.');
                return null;
            }

            // Initialize QuickBooks DataService
            $dataService = DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientID' => env('QB_CLIENT_ID'),
                'ClientSecret' => env('QB_CLIENT_SECRET'),
                'RedirectURI' => env('QB_REDIRECT_URI'),
                'accessTokenKey' => $accessToken,
                'refreshTokenKey' => Cache::get('qb_refresh_token'),
                'QBORealmID' => env('QB_REALM_ID'),
                'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com"
            ]);

            $dataService->setLogLocation(storage_path('logs/quickbooks.log'));

            // Check if customer exists
            $query = "SELECT * FROM Customer WHERE DisplayName = '$businessName'";
            $existingCustomers = $dataService->Query($query);

            $customer = null;
            if ($existingCustomers && count($existingCustomers) > 0) {
                $customer = $existingCustomers[0];
            } else {
                $customerData = Customer::create([
                    "CompanyName" => $businessName,
                    "DisplayName" => $businessName,
                    // "PrimaryEmailAddr" => ["Address" => $email]
                ]);

                $customer = $dataService->Add($customerData);
            }

            if (!$customer) {
                Log::error('Customer creation failed.');
                return null;
            }

            $customerId = $customer->Id;

            $services = $load->assignedServices->map(function ($service) {
                return [
                    "amount" => ($service->cost ?? $service->service->cost) * ($service->quantity ?? 1),
                    "id" =>  1,
                    "description" => 'This is for ' . ($service->service->service_type ?? 'freight'),
                    "quantity" => $service->quantity ?? 1,
                    "rate" => $service->cost ?? $service->service->cost
                ];
            })->filter()->toArray();

            if (empty($services)) {
                Log::error("No assigned services found for Load ID: {$load->id}");
                return null;
            }

            // Prepare invoice line items
            $lineItems = [];
            foreach ($services as $service) {
                $lineItems[] = [
                    "Amount" => $service['amount'],
                    "DetailType" => "SalesItemLineDetail",
                    "SalesItemLineDetail" => [
                        "ItemRef" => ["value" => $service['id']],
                        "Qty" => $service['quantity'],
                        "UnitPrice" => $service['rate']
                    ],
                    "Description" => $service['description']
                ];
            }
            // dd($lineItems);
            // Create invoice in QuickBooks
            $invoiceData = Invoice::create([
                "DocNumber" => $load->aol_number,
                "Line" => $lineItems,
                "CustomerRef" => ["value" => $customerId]
            ]);

            $resultingInvoice = $dataService->Add($invoiceData);

            if (!$resultingInvoice) {
                $error = $dataService->getLastError();
                Log::error('QuickBooks Invoice Creation Failed: ' . json_encode($error));
                return null;
            }
            $invoiceId = $resultingInvoice->Id;
            return response()->json(['invoice_id' => $invoiceId], 200);
        } catch (\Exception $e) {
            Log::error('Invoice creation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function showQuickBooksInvoice($load_id)
    {
        $en = $load_id;
        $de = decode_id($load_id);
        $load = Load::with('invoices')->find($de);

        if (!$load) {
            return redirect()->back()->with('error', 'Load not found.');
        }

        // Get QuickBooks Access Token
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return redirect()->back()->with('error', 'QuickBooks authentication failed.');
        }

        // Initialize QuickBooks DataService
        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => env('QB_CLIENT_ID'),
            'ClientSecret' => env('QB_CLIENT_SECRET'),
            'RedirectURI' => env('QB_REDIRECT_URI'),
            'accessTokenKey' => $accessToken,
            'refreshTokenKey' => Cache::get('qb_refresh_token'),
            'QBORealmID' => env('QB_REALM_ID'),
            'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com"
        ]);

        $dataService->setLogLocation(storage_path('logs/quickbooks.log'));

        $quickBooksInvoices = [];

        foreach ($load->invoices as $invoice) {
            if ($invoice->external_invoice_id) {
                // Fetch Invoice from QuickBooks
                $qbInvoice = $dataService->FindById('Invoice', $invoice->external_invoice_id);

                if ($qbInvoice) {
                    // Fetch Customer details
                    $customer = $dataService->FindById('Customer', $qbInvoice->CustomerRef);

                    // Fetch Services (Line Items)
                    $services = [];
                    if (!empty($qbInvoice->Line)) {
                        foreach ($qbInvoice->Line as $lineItem) {
                            $services[] = [
                                'description' => $lineItem->Description ?? 'N/A',
                                'amount' => $lineItem->Amount ?? 0,
                                'quantity' => $lineItem->SalesItemLineDetail->Qty ?? 1,
                                'rate' => $lineItem->SalesItemLineDetail->UnitPrice ?? 0,
                            ];
                        }
                    }

                    $quickBooksInvoices[] = [
                        'invoice' => $qbInvoice,
                        'customer' => $customer ?? null,
                        'services' => $services,
                    ];
                }
            }
        }

        return view('loads.quickbooks_invoices', compact('load', 'quickBooksInvoices'));
    }


    // Show Upload Form
    public function showUploadBillForm($load_id)
    {
        $en = $load_id;
        $de = decode_id($load_id);
        $load = Load::findOrFail($de);
        $aol = $load->aol_number;
        $user = auth()->user();
        $userType = $user->roledata->user_type_id;
        if ($userType != 3) {
            Session::flash('message', "You don't have permission to access this page.");
            return redirect()->route('dashboard')->with('error', 'Access Denied!');
        }
        $supplierId = auth()->user()->supplier_id;

       $assignedServices = AssignedService::where('load_id', $de)
        ->whereHas('supplier', function ($query) use ($supplierId) {
            $query->where('id', $supplierId);
        })
        ->get();

        return view('quickbooks.upload_bill', compact('assignedServices', 'load_id', 'aol'));
    }

    // Get or Create Supplier
    public function getOrCreateSupplier($supplierName, $supplierEmail, $dataService)
    {
        $query = "SELECT * FROM Vendor WHERE DisplayName = '" . $supplierName . "'";
        $existingSupplier = $dataService->Query($query);
        //   dd($existingSupplier,$supplierEmail,  "shdfskd");

        if (!empty($existingSupplier)) {
            return $existingSupplier[0]->Id; // Return existing supplier ID
        }

        // Create a new supplier
        $supplierData = Vendor::create([
            "DisplayName" => $supplierName,
            // "PrimaryEmailAddr" => ["Address" => $supplierEmail]
        ]);

        $newSupplier = $dataService->Add($supplierData);

        return $newSupplier ? $newSupplier->Id : null;
    }

    // Create a Bill and Upload PDF
    //   public function createSupplierBill(Request $request, $load_id)
    //   {
    //     $en = $load_id;
    //     $de = decode_id($load_id);
    //     $load = Load::findOrFail($de);
    //     $user = auth()->user();
    //     $suppliers = Supplier::where('user_id', $user->id )->get();

    //       $request->validate([
    //           'bill_pdf' => 'required'
    //       ]);

    //       $supplierName = $suppliers->supplier_name;
    //       $supplierEmail = $suppliers->supplier_email;

    //     $supplierName = 'test';
    //     $supplierEmail = 'test.developer.03@gmail.com';
    //     $amount = 100;
    //     $serviceDescription = 'test description';

    //       $pdfFile = $request->file('bill_pdf');

    //       // Get QuickBooks Access Token
    //       $accessToken = $this->getAccessToken();
    //       if (!$accessToken) {
    //           return redirect()->back()->with('error', 'QuickBooks authentication failed.');
    //       }

    //       // Initialize QuickBooks DataService
    //       $dataService = DataService::Configure([
    //           'auth_mode' => 'oauth2',
    //           'ClientID' => env('QB_CLIENT_ID'),
    //           'ClientSecret' => env('QB_CLIENT_SECRET'),
    //           'RedirectURI' => env('QB_REDIRECT_URI'),
    //           'accessTokenKey' => $accessToken,
    //           'refreshTokenKey' => Cache::get('qb_refresh_token'),
    //           'QBORealmID' => env('QB_REALM_ID'),
    //           'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com"
    //       ]);

    //       // Get or Create Supplier
    //       $supplierId = $this->getOrCreateSupplier($supplierName, $supplierEmail, $dataService);

    //       if (!$supplierId) {
    //           return redirect()->back()->with('error', 'Supplier could not be created.');
    //       }
    // // dd($supplierId);
    //       // Create a bill object
    //     //   $billData = Bill::create([
    //     //       "VendorRef" => ["value" => $supplierId],
    //     //      "Line" => [
    //     //     [
    //     //         "DetailType" => "SalesItemLineDetail", // Use Item-Based Expense Detail
    //     //         "Amount" => 500,
    //     //         "Description" => $serviceDescription,
    //     //         "SalesItemLineDetail" => [
    //     //             "ItemRef" => ["value" => 1], // Replace with the actual item ID from QuickBooks
    //     //             "UnitPrice" => 100.00, // Replace with actual unit price
    //     //             "Qty" => 5 // Replace with actual quantity
    //     //         ]
    //     //     ]
    //     // ]
    //     //   ]);
    //     $serviceDescription = 'Consulting service provided';
    //     $amount = 500;  // Total bill amount
    //     $itemId = 1;    // The Item ID for the service you created in QuickBooks
    //     $unitPrice = 100.00; // Unit price for the service
    //     $quantity = 5;  // The number of units for the service

    //     // Create the bill
    //     // $billData = Bill::create([
    //     //     "VendorRef" => ["value" => $supplierId],  // The Vendor (supplier) ID
    //     //     "Line" => [
    //     //         [
    //     //             "DetailType" => "ItemBasedExpenseLineDetail",  // Specify item-based expense
    //     //             "Amount" => $amount,  // Total amount for the service
    //     //             "Description" => $serviceDescription,  // Description of the service
    //     //             "ItemBasedExpenseLineDetail" => [
    //     //                 "ItemRef" => ["value" => $itemId],  // The ID of the service item
    //     //                 "UnitPrice" => $unitPrice,  // The unit price for the service
    //     //                 "Qty" => $quantity  // Quantity of the service
    //     //             ]
    //     //         ]
    //     //     ]
    //     // ]);
    //     // $billData = Bill::create([
    //     //     "Line" =>[
    //     //             [
    //     //                 "Id" =>"1",
    //     //                 "Amount" => 200.00,
    //     //                 "DetailType" => "AccountBasedExpenseLineDetail",
    //     //                 "AccountBasedExpenseLineDetail"=>
    //     //                 [
    //     //                     "AccountRef"=>
    //     //                     [
    //     //                         "value"=>"7"
    //     //                     ]
    //     //                 ]
    //     //             ]
    //     //         ],
    //     //         "VendorRef"=>
    //     //         [
    //     //             "value"=>$supplierId
    //     //         ]
    //     //   ]);
    //     //   $bill = $dataService->Add($billData);
    //     //   try {
    //     //     $result = $dataService->Add($billData);

    //     //     if ($result) {
    //     //         return redirect()->back()->with('success', 'Bill created successfully in QuickBooks.');
    //     //     } else {
    //     //         // Handle error
    //     //         $errors = $dataService->getLastError();
    //     //         return redirect()->back()->with('error', 'Failed to create bill. Error: ' . $errors->getResponseBody());
    //     //     }
    //     // } catch (\Exception $e) {
    //     //     return redirect()->back()->with('error', 'An error occurred while creating the bill: ' . $e->getMessage());
    //     // }
    //     //   if (!$bill) { 
    //     //       return redirect()->back()->with('error', 'Failed to create bill.');
    //     //   }
    // // dd("dsgkdf");
    //       // Upload PDF
    //       $file = $request->file('bill_pdf');

    //       // Check if file is uploaded
    //       if ($file && $file->isValid()) {
    //           // Save file in Laravel storage
    //           $filePath = $file->store('invoices', 'public'); // Save in public directory
    //           // Step 2: Get the full path to the file
    // $fullFilePath = storage_path('app/public/' . $filePath);  // This gives the complete path to the file on the server
    // // dd($fullFilePath);
    // // Step 3: Read the file content as binary data
    // // $fileContent = file_get_contents($fullFilePath); 
    // // $file = $request->file('bill_pdf');
    // // $base64Content = base64_encode(file_get_contents($file->getRealPath()));

    //           // Call the uploadBillPdf method to send the file to QuickBooks
    //         //   $uploadSuccess = $this->uploadBillPdf($dataService, $bill->Id, $base64Content);

    //         //   if ($uploadSuccess) {
    //         //       return response()->json(['message' => 'PDF uploaded successfully.']);
    //         //   } else {
    //         //       return response()->json(['message' => 'Failed to upload PDF.'], 500);
    //         //   }

    //         $file = $request->file('bill_pdf');

    // // Validate file
    // if (!$file->isValid()) {
    //     return back()->with('error', 'Invalid file upload.');
    // }

    // // Get File Information
    // $fileName = "billabc111." . $file->getClientOriginalExtension();
    // $mimeType = $file->getMimeType();
    // $fileContent = file_get_contents($file->getRealPath());

    // // Debugging
    // // dd([
    // //     'File Name' => $fileName,
    // //     'MIME Type' => $mimeType,
    // //     'File Size' => $file->getSize(),
    // // ]);

    // // Prepare Data
    // $entityRef = new IPPReferenceType(['value' => 263, 'type' => 'Bill']);
    // $attachableRef = new IPPAttachableRef(['EntityRef' => $entityRef]);

    // $objAttachable = new IPPAttachable();
    // $objAttachable->FileName = $fileName;
    // $objAttachable->AttachableRef = $attachableRef;
    // $objAttachable->Category = 'Receipt';
    // $objAttachable->Tag = 'Tag_' . time();

    // // Upload File to QuickBooks
    // $resultObj = $dataService->Upload(
    //     base64_encode($fileContent),  // Pass raw binary, not base64
    //     $objAttachable->FileName,
    //     $mimeType,
    //     $objAttachable
    // );

    // dd($resultObj);
    //       } else {
    //           return response()->json(['message' => 'No file uploaded or file is invalid.'], 400);
    //       }

    //       return redirect()->back()->with('success', 'Bill created successfully and PDF uploaded.');
    //   }


    public function uploadBillPdf($dataService, $billId, $base64Content)
    {
        //   // Generate a random ID for the file name
        //   $randId = rand(); // Generate a random ID for the file name
        //   $fileName = 'bill_' . $randId . '.pdf'; // PDF file name
        //   $sendMimeType = 'application/pdf'; // MIME Type for PDF

        //   // Read the file content
        //   $fileContent = file_get_contents($filePath);

        //   // Check if the file exists and has content
        //   if (!$fileContent) {
        //       error_log('File not found or empty: ' . $filePath);
        //       return false;
        //   }
        //   if (empty($fileContent)) {
        //     error_log('Error: The PDF file content is empty.');
        //     return false;
        // }
        //   // Create a reference to the Bill using $billId (ensure this is the correct ID)
        //   $entityRef = new IPPReferenceType([
        //       'value' => $billId,  // The bill ID for the attachment
        //       'type' => 'Bill' // Specify the entity type is 'Bill'
        //   ]);

        //   // Prepare the attachable reference (this links the attachment to the bill)
        //   $attachableRef = new IPPAttachableRef([
        //       'EntityRef' => $entityRef
        //   ]);

        //   // Create the attachable object (the attachment that will be uploaded)
        //   $objAttachable = new IPPAttachable();
        //   $objAttachable->FileName = $fileName;
        //   $objAttachable->AttachableRef[] = $attachableRef;
        //   $objAttachable->Category = 'Attachment';
        //   $objAttachable->Tag = 'Invoice_PDF_' . $randId;

        //   // Upload the PDF attachment to QuickBooks Bill
        //   try {
        //       $response = $dataService->Upload(
        //           $fileContent, // Binary PDF content
        //           $fileName,    // File name
        //           $sendMimeType, // MIME Type (application/pdf)
        //           $objAttachable // Attachable object with reference to the bill
        //       );

        //       // Check if the response is successful
        //       if ($response) {
        //           return true;
        //       } else {
        //           error_log('QuickBooks upload failed: ' . json_encode($response));
        //           return false;
        //       }
        //   } catch (Exception $e) {
        //       // Handle errors (e.g., connection issues, QuickBooks API errors)
        //       error_log('QuickBooks Upload Error: ' . $e->getMessage());
        //       return false; // Return false if there was an error uploading the file
        //   }


        $imageBase64 = array();
        $imageBase64['image/jpeg'] = $base64Content;

        $sendMimeType = "image/jpeg";

        $randId = rand();
        $entityRef = new IPPReferenceType(array('value' => $billId, 'type' => 'Bill'));
        $attachableRef = new IPPAttachableRef(array('EntityRef' => $entityRef));
        $objAttachable = new IPPAttachable();
        $objAttachable->FileName = $randId . ".jpg";
        $objAttachable->AttachableRef = $attachableRef;
        $objAttachable->Category = 'Image';
        $objAttachable->Tag = 'Tag_' . $randId;

        // Upload the attachment to the Bill
        $resultObj = $dataService->Upload(
            base64_decode($imageBase64[$sendMimeType]),
            $objAttachable->FileName,
            $sendMimeType,
            $objAttachable
        );

        dd($resultObj);
    }

    public function createSupplierBill(Request $request, $load_id)
    {
        $en = $load_id;
        $de = decode_id($load_id);
        $load = Load::findOrFail($de);
        $request->validate([
            'bill_pdf' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'bill_no' => 'required|string|max:255',
        ]);
        $user = auth()->user();

        $suppliers = Supplier::with('user')
        ->where('id', $user->supplier_id)
        ->first();     
        
        // dd($suppliers);
        if ($request->hasFile('bill_pdf')) {
            $file = $request->file('bill_pdf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $request->file('bill_pdf')->store('uploads/bills', 'public');
        } else {
            return back()->with('error', 'File upload failed.');
        }

        // Insert into SupplierInvoice table
        $supplierInvoice = SupplierInvoice::create([
            'load_id' => $load->id,
            'supplier_id' => $suppliers->id,
            'file_path' => $filePath,
            'quickbook_invoice_id' => null,
            'status' => 'pending',
            'bill_no' => $request->input('bill_no'),
        ]);

        // Optional: Update Load with invoice ID
        $load->update(['supplier_invoice_id' => $supplierInvoice->id]);

        return back()->with('message',  __('messages.bill_uploaded_successfully'));
    }



    public function uploadToQuickBooks($invoice)
    {
        try {
            $loadId = $invoice['load_id'];
            // $loadId = 71;
            $supplier_id = $invoice['supplier_id'];
            // $supplier_id = 18;
            $load = Load::findOrFail($loadId);
            $suppliers = Supplier::where('id', $supplier_id)->first();

            $supplierName = $suppliers->company_name;
            // $supplierEmail = $suppliers->user_email;
            $supplierEmail = 'test@gmail.com';

            // Get QuickBooks Access Token
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error('QuickBooks authentication failed for loadId: ' . $loadId . ' and supplierId: ' . $supplier_id);
                return;
            }
            // Initialize QuickBooks DataService
            $dataService = DataService::Configure([
                'auth_mode' => 'oauth2',
                'ClientID' => env('QB_CLIENT_ID'),
                'ClientSecret' => env('QB_CLIENT_SECRET'),
                'RedirectURI' => env('QB_REDIRECT_URI'),
                'accessTokenKey' => $accessToken,
                'refreshTokenKey' => Cache::get('qb_refresh_token'),
                'QBORealmID' => env('QB_REALM_ID'),
                'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com"
            ]);

            // Get or Create Supplier
            $supplierId = $this->getOrCreateSupplier($supplierName, $supplierEmail, $dataService);

            if (!$supplierId) {
                Log::error('Supplier creation failed for loadId: ' . $loadId . ' and supplierId: ' . $supplier_id);
                return;
            }

            $assignedServices = AssignedService::where('load_id', $loadId)
                ->whereHas('supplier', function ($query) use ($supplier_id) {
                    $query->where('id', $supplier_id); // Filter based on the user_id of the supplier
                })
                ->with(['supplier', 'service.masterService']) // Eager load the related 'supplier' and 'service' models
                ->get();


            $lines = [];

            foreach ($assignedServices as $assignedService) {
                $serviceDescription = 'This is for ' . ($assignedService->service->masterService->service_type ?? 'freight'); 
                $amount = ($assignedService->supplier_cost ) * ($assignedService->quantity ?? 1);  
                $unitPrice = $assignedService->supplier_cost ; 
                $quantity = $assignedService->quantity;  

                // Build the Line data dynamically
                $lines[] = [
                    "DetailType" => "ItemBasedExpenseLineDetail",
                    "Amount" => $amount,
                    "Description" => $serviceDescription,
                    "ItemBasedExpenseLineDetail" => [
                        "ItemRef" => ["value" => 1],
                        "UnitPrice" => $unitPrice,
                        "Qty" => $quantity,
                    ],
                ];
            }

            $billData = Bill::create([
                "VendorRef" => ["value" => $supplierId],
                "Line" => $lines,
                "DocNumber" => $invoice['bill_no'],
            ]);

            $bill = $dataService->Add($billData);

            $file_path_from_db  = 'public/'.$invoice['file_path'];
            // Use the Laravel Storage facade to get the file content
            if (Storage::exists($file_path_from_db)) {
                $fileContent = Storage::get($file_path_from_db);
                $fileExtension = pathinfo($file_path_from_db, PATHINFO_EXTENSION);

                // Define the file name (you can generate it dynamically if needed)
                $fileName = "bill" . time() . "." . $fileExtension;
                $mimeType = mime_content_type(storage_path('app/' . $file_path_from_db));  // Getting mime type

                // Create an entity reference for QuickBooks (e.g., a Bill entity)
                $entityRef = new IPPReferenceType(['value' => $bill->Id, 'type' => 'Bill']);
                $attachableRef = new IPPAttachableRef(['EntityRef' => $entityRef]);

                // Create an attachable object
                $objAttachable = new IPPAttachable();
                $objAttachable->FileName = $fileName;
                $objAttachable->AttachableRef = $attachableRef;
                $objAttachable->Category = 'Receipt';
                $objAttachable->Tag = 'Tag_' . time();

                // Upload File to QuickBooks
                try {
                    $resultObj = $dataService->Upload(
                        base64_encode($fileContent),  // Pass raw binary, not base64
                        $objAttachable->FileName,
                        $mimeType,
                        $objAttachable
                    );

                    // Handle the result, e.g., saving the QuickBooks file reference to the database
                    if ($resultObj) {
                        Log::info('File uploaded successfully to QuickBooks for loadId: ' . $loadId . ' and supplierId: ' . $supplier_id);

                    } else {
                        Log::error('Failed to upload the file to QuickBooks for loadId: ' . $loadId . ' and supplierId: ' . $supplier_id);
                    }
                } catch (\Exception $e) {
                    Log::error('Error during file upload to QuickBooks: ' . $e->getMessage());
                }
            } else {
                Log::error('File not found for loadId: ' . $loadId . ' and supplierId: ' . $supplier_id);
            }
            return response()->json(['billId' => $bill->Id]);
        } catch (\Exception $e) {
            Log::error('Error during QuickBooks upload process: ' . $e->getMessage());
            return;
        }
    }

    public function showQuickBooksBillByLoadId($load_id)
{
    $en = $load_id;
    $de = decode_id($load_id);
    $load = Load::findOrFail($de);
    $en = $load_id;
    $de = decode_id($load_id);
    if (!$load) {
        return redirect()->back()->with('error', 'Load not found.');
    }
     // Fetch the SupplierInvoice record with the given load_id
     $supplierInvoice = SupplierInvoice::where('load_id', $de)
     ->latest()
     ->first();
     if (!$supplierInvoice) {
        return redirect()->back()->with('error', 'supplier Invoice not found.');
    }
     if (!$supplierInvoice) {
         return view('quickbooks.bill_details')->with('error', 'No supplier invoice found for this load.');
     }
 
     // Get the QuickBooks Invoice ID (Bill ID)
     $billId = $supplierInvoice->quickbook_invoice_id;
 
     if (!$billId) {
         return view('quickbooks.bill_details')->with('success', 'The bill is being uploaded to QuickBooks. Please refresh the page after a short while.');

     }
 
    // Get QuickBooks Access Token
    $accessToken = $this->getAccessToken();
    if (!$accessToken) {
        return view('quickbooks.bill_details')->with('error', 'QuickBooks authentication failed.');
    }

    // Initialize QuickBooks DataService
    $dataService = DataService::Configure([
        'auth_mode' => 'oauth2',
        'ClientID' => env('QB_CLIENT_ID'),
        'ClientSecret' => env('QB_CLIENT_SECRET'),
        'RedirectURI' => env('QB_REDIRECT_URI'),
        'accessTokenKey' => $accessToken,
        'refreshTokenKey' => Cache::get('qb_refresh_token'),
        'QBORealmID' => env('QB_REALM_ID'),
        'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com"
    ]);

    try {
        // Fetch Bill Details
        $quickBooksBill = $dataService->FindById('Bill', $billId);
        $attachments = $dataService->Query("SELECT * FROM Attachable WHERE AttachableRef.EntityRef.value ='$billId'");
        return view('quickbooks.bill_details', compact('quickBooksBill', 'attachments'));
        // Fetch Vendor Details
        // $vendorId = $quickBooksBill->VendorRef->value ?? null;
        // $vendorInfo = $vendorId ? $dataService->FindById('Vendor', '71') : null;
        // dd($vendorInfo);


    } catch (\Exception $e) {
        return view('quickbooks.bill_details')->with('error', 'Error fetching bill or vendor from QuickBooks: ' . $e->getMessage());
    }
}


protected function updateQuickBooksTokensInDB($accessToken, $refreshToken, $expiresAt)
{
      if (Schema::hasTable('quickbooks_tokens')) {
    QuickbooksToken::updateOrCreate(
        ['id' => 1], // Adjust if using multiple records
        [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'access_token_expires_at' => $expiresAt,
        ]
    );
}
}

}
