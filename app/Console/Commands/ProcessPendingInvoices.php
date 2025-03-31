<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Load;
use App\Services\QuickBooksService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\QuickBooksController;


class ProcessPendingInvoices extends Command
{
    protected $signature = 'invoices:process-pending';
    protected $description = 'Process pending invoices and update their status with QuickBooks invoice ID';

    public function __construct(protected QuickBooksService $qbService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $pendingInvoices = Invoice::where('status', 'pending')->get();

        if ($pendingInvoices->isEmpty()) {
            Log::info('No pending invoices found.');
            return;
        }
        $quickBooksController = app(QuickBooksController::class);

        foreach ($pendingInvoices as $invoice) {
            try {
                $load = Load::find($invoice->load_id);

                if (!$load) {
                    Log::error("Load not found for Invoice ID: {$invoice->id}");
                    continue;
                }

                $response = $quickBooksController->addClientInvoice($invoice->load_id);


                if ($response && isset($response->original['invoice_id'])) {
                    $invoice->update([
                        'external_invoice_id' => $response->original['invoice_id'],
                        'status' => 'processed',
                    ]);
                    $load->invoice_id = $invoice->id;
                    $load->save();
                    Log::info("Invoice ID {$invoice->id} updated successfully with QuickBooks Invoice ID {$response->original['invoice_id']}.");
                } else {
                    Log::error("Failed to process Invoice ID {$invoice->id}.");
                }
            } catch (\Exception $e) {
                Log::error("Error processing Invoice ID {$invoice->id}: " . $e->getMessage());
            }
        }
    }
}
