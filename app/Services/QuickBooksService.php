<?php

namespace App\Services;

use QuickBooksOnline\API\DataService\DataService;
use Illuminate\Support\Facades\Cache;

class QuickBooksService
{
    private $dataService;

    public function __construct()
    {
        $this->dataService = DataService::Configure([
            'auth_mode'    => 'oauth2',
            'ClientID'     => env('QUICKBOOKS_CLIENT_ID'),
            'ClientSecret' => env('QUICKBOOKS_CLIENT_SECRET'),
            'RedirectURI'  => env('QUICKBOOKS_REDIRECT_URI'),
            'scope'        => "com.intuit.quickbooks.accounting",
            'baseUrl'      => env('QUICKBOOKS_ENVIRONMENT') === 'sandbox' ? "development" : "production"
        ]);

        $this->refreshAccessToken();
    }

    public function getAuthorizationUrl()
    {
        return $this->dataService->getOAuth2LoginHelper()->getAuthorizationCodeURL();
    }

    public function handleCallback($code)
    {
        $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
        $accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, env('QB_CLIENT_ID'), env('QB_CLIENT_SECRET'));

        $this->storeToken($accessTokenObj);
    }

    private function storeToken($accessTokenObj)
    {
        Cache::put('qb_access_token', $accessTokenObj->getAccessToken(), now()->addSeconds($accessTokenObj->getAccessTokenExpiresAt()));
        Cache::put('qb_refresh_token', $accessTokenObj->getRefreshToken(), now()->addDays(90));
        Cache::put('qb_realm_id', $accessTokenObj->getRealmID());
    }

    public function refreshAccessToken()
    {
        $refreshToken = Cache::get('qb_refresh_token');

        if (!$refreshToken) {
            return false;
        }

        $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
        $newAccessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($refreshToken);

        if ($newAccessTokenObj) {
            $this->storeToken($newAccessTokenObj);
            return true;
        }

        return false;
    }

    public function getDataService()
    {
        $accessToken = Cache::get('qb_access_token');

    if (!$accessToken) {
        \Log::error("QuickBooks Access Token is missing.");
        return null;
    }

    try {
        $this->dataService->updateOAuth2Token($accessToken);
        return $this->dataService;
    } catch (\Exception $e) {
        \Log::error("QuickBooks Authentication Error: " . $e->getMessage());
        return null;
    }
    }
}
