<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\QuickBooksController;

class RefreshQuickBooksToken extends Command

{
    protected $signature = 'quickbooks:refresh-token';
    protected $description = 'Refresh QuickBooks API Access Token using QuickBooksController';

    public function handle()
    {
        $controller = new QuickBooksController();
        $response = $controller->refreshAccessToken();

        if (is_string($response)) {
            $this->info('QuickBooks access token refreshed successfully.');
            $this->info('New Access Token: ' . $response);
        } elseif ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            if (isset($data['error'])) {
                $this->error('Failed to refresh QuickBooks token: ' . $data['error']);
            } else {
                $this->info('QuickBooks access token refreshed successfully.');
                $this->info('New Access Token: ' . ($data['access_token'] ?? 'Not Available'));
            }
        } else {
            $this->error('Unexpected response from QuickBooks API.');
        }
    }
}
