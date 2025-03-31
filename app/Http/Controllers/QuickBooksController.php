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

class QuickBooksController extends Controller
{
    // ✅ Function to refresh QuickBooks Access Token
    private function refreshAccessToken()
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
        $fullFilePath = storage_path("app/public/" . $filePath);

        $fileType = mime_content_type($fullFilePath);

        $fileMimeType = $file->getMimeType();
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

        // Store new tokens in cache
        Cache::put('qb_access_token', $accessTokenObj->getAccessToken(), now()->addMinutes(50));
        Cache::put('qb_refresh_token', $accessTokenObj->getRefreshToken(), now()->addDays(30));

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


//     public function addClientInvoice($load_id)
//     {
//         $en = $load_id;
//         $de = decode_id($load_id);
//         $load = Load::with(['creatorfor', 'assignedServices.service'])->findOrFail($de);
//         $businessName = optional($load->creatorfor)->business_name ?? optional($load->creatorfor)->email;
//         $email = $load->creatorfor->email;

//         // Get QuickBooks Access Token
//         $accessToken = $this->getAccessToken();
//         if (!$accessToken) {
//             return back()->withErrors(['error' => 'QuickBooks authentication failed.']);
//         }

//         // Initialize QuickBooks DataService
//         $dataService = DataService::Configure([
//             'auth_mode' => 'oauth2',
//             'ClientID' => env('QB_CLIENT_ID'),
//             'ClientSecret' => env('QB_CLIENT_SECRET'),
//             'RedirectURI' => env('QB_REDIRECT_URI'),
//             'accessTokenKey' => $accessToken,
//             'refreshTokenKey' => Cache::get('qb_refresh_token'),
//             'QBORealmID' => env('QB_REALM_ID'),
//             'baseUrl' => env('QB_ENV') === 'production' ? "https://quickbooks.api.intuit.com" : "https://sandbox-quickbooks.api.intuit.com"
//         ]);

//         $dataService->setLogLocation(storage_path('logs/quickbooks.log'));

//         $query = "SELECT * FROM Customer WHERE PrimaryEmailAddr = '$email'";
//         $existingCustomers = $dataService->Query($query);

//         $customer = null;
//     if ($existingCustomers && count($existingCustomers) > 0) {
//         $customer = $existingCustomers[0];
//     } else {
//         $customerData = Customer::create([
//             "CompanyName" => $businessName,
//             "DisplayName" => $businessName,
//             "PrimaryEmailAddr" => ["Address" => $email]
//         ]);

//         $customer = $dataService->Add($customerData);
//     }
//         $customerId = $customer->Id;

//         $services = $load->assignedServices->map(function ($service) {
//             return [
//                 "amount" => ($service->cost ?? $service->service->cost) * ($service->quantity ?? 1),
//                 "id" => 1, 
//                 "description" => 'This is for '.$service->service->service_type ?? 'freight', 
//                 "quantity" => $service->quantity ?? 1, 
//                 "rate" => $service->cost ?? $service->service->cost
//             ];
//         })->toArray();
        
//         // Now, dynamically create the Line items
//         $lineItems = [];
        
//         foreach ($services as $service) {
//             $lineItems[] = [
//                 "Amount" => $service['amount'],
//                 "DetailType" => "SalesItemLineDetail",
//                 "SalesItemLineDetail" => [
//                     "ItemRef" => ["value" => $service['id']],
//                     "Qty" => $service['quantity'],
//                     "UnitPrice" =>$service['rate']
//                 ],
//                 "Description" => $service['description']
//             ];
//         }
//         // dd($lineItems);
//         // Use $lineItems in your invoice creation
//         $invoiceData = Invoice::create([
//             "DocNumber" => $load->aol_number,
//             "Line" => $lineItems,
//             "CustomerRef" => ["value" => $customerId] // Replace with actual customer ID
//         ]);
//         // Send Invoice to QuickBooks
//         $resultingInvoice = $dataService->Add($invoiceData);
//         $invoiceId = $resultingInvoice->Id; 
// ;
//         dd($invoiceId);
//         if (!$resultingInvoice) {
//             return back()->withErrors(['error' => 'QuickBooks Invoice Creation Failed: ' . $dataService->getLastError()]);
//         }

//         return redirect()->route('invoice.upload', $request->load_id)->with('success', 'Invoice uploaded successfully!');
//     }

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
        $email = $load->creatorfor->email;

        // Get QuickBooks Access Token
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('QuickBooks authentication failed.');
            return null;        }

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
        $query = "SELECT * FROM Customer WHERE PrimaryEmailAddr = '$email'";
        $existingCustomers = $dataService->Query($query);

        $customer = null;
        if ($existingCustomers && count($existingCustomers) > 0) {
            $customer = $existingCustomers[0];
        } else {
            $customerData = Customer::create([
                "CompanyName" => $businessName,
                "DisplayName" => $businessName,
                "PrimaryEmailAddr" => ["Address" => $email]
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
            return null;        }
        $invoiceId = $resultingInvoice->Id;
        return response()->json(['invoice_id' => $invoiceId], 200);
    } catch (\Exception $e) {
        Log::error('Invoice creation failed: ' . $e->getMessage());
        return null;    }
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

}