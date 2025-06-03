<?php

namespace App\Services;

use Google_Client;
use Google_Service_Webmasters;
use Google_Service_Webmasters_SearchAnalyticsQueryRequest;

class SearchConsoleService
{
    protected $client;
    protected $webmasters;

    public function __construct(string $credentialsPath, string $accessToken)
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->setAccessToken($accessToken);
        $this->client->addScope('https://www.googleapis.com/auth/webmasters');

        $this->webmasters = new Google_Service_Webmasters($this->client);
    }

    public function getSearchAnalytics(string $siteUrl, string $startDate, string $endDate)
    {
        $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dimensions' => ['date', 'query', 'page', 'country', 'device'],
            'rowLimit' => 25000
        ]);

        return $this->webmasters->searchanalytics->query($siteUrl, $request);
    }

    public function inspectUrl(string $siteUrl, string $url)
    {
        // Use Google Indexing API or URL Inspection API via custom Guzzle if available
        // Placeholder - requires OAuth and Indexing API
        return [];
    }
}
