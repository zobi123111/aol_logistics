<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupplierService;
use Carbon\Carbon;

class UpdateServiceCost extends Command
{
    protected $signature = 'services:update-cost';
    protected $description = 'Update service cost based on the schedule cost when the service date matches the current date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get the current date in Y-m-d format
        $today = Carbon::today()->format('Y-m-d');

        // Update services where the service_date matches today
        $services = SupplierService::where('service_date', $today)->get();

        foreach ($services as $service) {
            // Update the cost and clear the schedule details
            $service->update([
                'cost' => $service->schedule_cost,
                'schedule_cost' => null,
                'service_date' => null,
            ]);

            $this->info("Updated service ID: {$service->id} (Cost: {$service->cost})");
        }

        $this->info("Service cost update completed.");
        return Command::SUCCESS;
    }
}