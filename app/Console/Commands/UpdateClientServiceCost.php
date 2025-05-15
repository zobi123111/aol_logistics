<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientService;
use Carbon\Carbon;

class UpdateClientServiceCost extends Command
{
    protected $signature = 'client-services:update-cost';
    protected $description = 'Update client service cost based on the schedule cost when the service date matches the current date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get the current date in Y-m-d format
        $today = Carbon::today()->format('Y-m-d');

        // Update client services where the service_date matches today
        $services = ClientService::where('service_date', $today)->get();

        foreach ($services as $service) {
            // Update the cost and clear the schedule details
            $service->update([
                'cost' => $service->schedule_cost,
                'schedule_cost' => null,
                'service_date' => null,
            ]);

            $this->info("Updated client service ID: {$service->id} (Cost: {$service->cost})");
        }

        $this->info("Client service cost update completed.");
        return Command::SUCCESS;
    }
}
