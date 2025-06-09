<?php

namespace App\Services;

use Google_Client;
use Google_Service_Webmasters;
use Google_Service_Webmasters_SearchAnalyticsQueryRequest;
use Illuminate\Support\Facades\Http;
use \Illuminate\Http\Client\Response;


class GoogleService
{
    protected Google_Client $client;
    protected string $accessToken;

    public function __construct(array $tokens)
    {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setAccessToken([
            'access_token' => $tokens['provider_token'],
            'refresh_token' => $tokens['refresh_token'],
            'created' => time(),
        ]);


        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($tokens['refresh_token']);
        }

        $this->accessToken = $this->client->getAccessToken()['access_token'];
    }

    public function getAccounts(): array
    {
        return Http::withToken($this->accessToken)
            ->get('https://analyticsadmin.googleapis.com/v1beta/accounts')
            ->json('accounts', []);
    }

    public function getProperties(string $accountId): array
    {
        return Http::withToken($this->accessToken)
            ->get("https://analyticsadmin.googleapis.com/v1beta/properties", [
                'filter' => "parent:{$accountId}"
            ])->json('properties', []);
    }

    public function getAnalyticsReport(string $propertyId, string $startDate, string $endDate): Response
    {
        return Http::withToken($this->accessToken)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [[
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]],
                'dimensions' => [['name' => 'sessionSourceMedium']],
                'metrics' => [['name' => 'sessions']],
            ]);
    }

    public function getDailyAnalyticsReport(string $propertyId, string $startDate, string $endDate): Response
    {
        return Http::withToken($this->accessToken)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [[
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]],
                'dimensions' => [['name' => 'date']],
                'metrics' => [['name' => 'sessions']],
            ]);
    }



    public function getWebmasterSites(): array
    {
        $service = new Google_Service_Webmasters($this->client);
        return $service->sites->listSites()->getSiteEntry();
    }

    public function getSearchAnalytics(string $siteUrl, string $startDate, string $endDate, int $limit = 1000): \Google_Service_Webmasters_SearchAnalyticsQueryResponse
    {
        $service = new Google_Service_Webmasters($this->client);

        $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dimensions' => ['date', 'query', 'page', 'country', 'device'],
            'rowLimit' => $limit,
            'aggregationType' => 'byPage'
        ]);

        return $service->searchanalytics->query($siteUrl, $request);
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getClient(): Google_Client
    {
        return $this->client;
    }


    public function getTopPages(string $propertyId, string $startDate, string $endDate, int $limit = 5): Response
    {
        return Http::withToken($this->accessToken)
            ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                'dateRanges' => [[
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]],
                'dimensions' => [['name' => 'pagePath']],
                'metrics' => [['name' => 'screenPageViews']],
                'orderBys' => [[
                    'metric' => ['metricName' => 'screenPageViews'],
                    'desc' => true
                ]],
                'limit' => $limit,
            ]);
    }

    public function getDetailedPagesAnalytics(string $propertyId, string $startDate, string $endDate, int $limit = 100000): array
    {
        $allRows = [];
        $offset = 0;

        do {
            $response = Http::withToken($this->accessToken)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$propertyId}:runReport", [
                    'dateRanges' => [[
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                    ]],
                    'dimensions' => [
                        ['name' => 'pagePath'],
                        ['name' => 'country'],
                        ['name' => 'sessionSourceMedium'],
                    ],
                    'metrics' => [
                        ['name' => 'screenPageViews'],
                        ['name' => 'sessions'],
                        ['name' => 'totalUsers'],
                        ['name' => 'bounceRate'],
                        ['name' => 'userEngagementDuration'],
                        ['name' => 'eventCount'],
                        ['name' => 'totalRevenue'],
                    ],
                    'limit' => $limit,
                    'offset' => $offset,
                ]);



            $data = $response->json();

            $rows = $data['rows'] ?? [];
            $rowCount = count($rows);
            $offset += $rowCount;
            $allRows = array_merge($allRows, $rows);
        } while ($rowCount === $limit);

        return ['rows' => $allRows];
    }

    public function getSeoIssues(string $siteUrl, string $pageUrl): array
    {
        $url = 'https://searchconsole.googleapis.com/v1/urlInspection/index:inspect';

        $response = Http::withToken($this->accessToken)->post($url, [
            'inspectionUrl' => $pageUrl,
            'siteUrl' => $siteUrl,
        ]);

        return $response->json('inspectionResult', []);
    }

    public function inspectUrl(string $siteUrl, string $pageUrl): array
    {
        $response = Http::withToken($this->accessToken)
            ->post('https://searchconsole.googleapis.com/v1/urlInspection/index:inspect', [
                'inspectionUrl' => $pageUrl,
                'siteUrl' => $siteUrl,
            ]);

        return $response->json();
    }

    public function getSiteUrls(string $siteUrl)
    {
        $service = new Google_Service_Webmasters($this->client); // FIXED: define service

        $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest([
            'startDate' => '2024-05-01',
            'endDate' => '2024-06-01',
            'dimensions' => ['page'],
            'rowLimit' => 5000
        ]);

        $response = $service->searchanalytics->query($siteUrl, $request);

        $urls = [];

        if (!empty($response->getRows())) {
            foreach ($response->getRows() as $row) {
                $dimensions = $row->getKeys();
                $urls[] = $dimensions[2] ?? $dimensions[0] ?? ''; // Use page dimension
            }
        }

        $totalUrls = count($urls);

        return [
            'total_indexed_urls' => $totalUrls,
            'pages' => $urls,
        ];
    }
}
