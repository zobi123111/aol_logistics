<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupplierInvoice;
use App\Http\Controllers\QuickBooksController;
use Illuminate\Support\Facades\Storage;
use App\Models\Load;


class ProcessSupplierInvoices extends Command
{
    protected $signature = 'supplier:process-invoices';
    protected $description = 'Process pending supplier invoices and upload them to QuickBooks';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pendingInvoices = SupplierInvoice::where('status', 'pending')->get();

        if ($pendingInvoices->isEmpty()) {
            $this->info('No pending invoices found.');
            return;
        }

        $quickBooksController = new QuickBooksController();

        foreach ($pendingInvoices as $invoice) {
            // Call the QuickBooksController function to process the invoice
            $quickbookResponse = $quickBooksController->uploadToQuickBooks($invoice);
            if ($quickbookResponse instanceof \Illuminate\Http\JsonResponse) {
                $quickbookResponse = $quickbookResponse->getData(true); // Convert to an associative array
            }
            
            if (isset($quickbookResponse['billId'])) {
                $invoice->update([
                    'quickbook_invoice_id' => $quickbookResponse['billId'],  
                    'status' => 'processed'
                ]);
                $this->info("Invoice ID {$invoice->id} processed successfully.");
            }
            else {
                    $this->error("Failed to upload invoice ID {$invoice->id} to QuickBooks.");
                }
        }
    }
}
