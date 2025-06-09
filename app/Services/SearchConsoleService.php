<?php

namespace App\Services;

use Google_Client;
use Google_Service_Webmasters;
use Google_Service_Webmasters_SearchAnalyticsQueryRequest;
use Google_Service_SearchConsole;
use Google_Service_SearchConsole_InspectUrlIndexRequest;

class SearchConsoleService
{
    protected $client;
    protected $webmasters;
    protected $searchConsole;

    public function __construct(string $credentialsPath, string $accessToken)
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->setAccessToken($accessToken);
        $this->client->addScope('https://www.googleapis.com/auth/webmasters');
        $this->client->addScope('https://www.googleapis.com/auth/webmasters.readonly');

        $this->webmasters = new Google_Service_Webmasters($this->client);
        $this->searchConsole = new Google_Service_SearchConsole($this->client);
    }

    public function getSearchAnalytics(string $siteUrl, string $startDate, string $endDate): array
    {
        $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dimensions' => ['query'],
            'rowLimit' => 100,
        ]);

        $response = $this->webmasters->searchanalytics->query($siteUrl, $request);
        $keywords = [];

        foreach ($response->getRows() as $row) {
            $keywords[] = [
                'query' => $row->getKeys()[0],
                'clicks' => $row->getClicks(),
                'impressions' => $row->getImpressions(),
                'ctr' => $row->getCtr(),
                'position' => $row->getPosition(),
            ];
        }

        return $keywords;
    }

    public function inspectUrl(string $url): array
    {
        $siteUrl = 'sc-domain:' . parse_url($url, PHP_URL_HOST);

        $request = new Google_Service_SearchConsole_InspectUrlIndexRequest([
            'inspectionUrl' => $url,
            'siteUrl' => $siteUrl,
        ]);

        try {
            $response = $this->searchConsole->urlInspection_index->inspect($request);
            return $response->toSimpleObject();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getIndexedPages(string $siteUrl): array
    {
        $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest([
            'startDate' => now()->subDays(28)->toDateString(),
            'endDate' => now()->toDateString(),
            'dimensions' => ['page'],
            'rowLimit' => 250,
        ]);

        $response = $this->webmasters->searchanalytics->query($siteUrl, $request);

        $pages = [];
        foreach ($response->getRows() as $row) {
            $pages[] = [
                'page' => $row->getKeys()[0],
                'clicks' => $row->getClicks(),
                'impressions' => $row->getImpressions(),
                'ctr' => $row->getCtr(),
                'position' => $row->getPosition(),
            ];
        }

        return $pages;
    }

    public function getCountriesTraffic(string $siteUrl): array
    {
        $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest([
            'startDate' => now()->subDays(28)->toDateString(),
            'endDate' => now()->toDateString(),
            'dimensions' => ['country'],
            'rowLimit' => 50,
        ]);

        $response = $this->webmasters->searchanalytics->query($siteUrl, $request);

        $countries = [];
        foreach ($response->getRows() as $row) {
            $countries[] = [
                'country' => $row->getKeys()[0],
                'clicks' => $row->getClicks(),
                'impressions' => $row->getImpressions(),
                'ctr' => $row->getCtr(),
                'position' => $row->getPosition(),
            ];
        }

        return $countries;
    }

    public function getDeviceTraffic(string $siteUrl): array
    {
        $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest([
            'startDate' => now()->subDays(28)->toDateString(),
            'endDate' => now()->toDateString(),
            'dimensions' => ['device'],
            'rowLimit' => 10,
        ]);

        $response = $this->webmasters->searchanalytics->query($siteUrl, $request);

        $devices = [];
        foreach ($response->getRows() as $row) {
            $devices[] = [
                'device' => $row->getKeys()[0],
                'clicks' => $row->getClicks(),
                'impressions' => $row->getImpressions(),
                'ctr' => $row->getCtr(),
                'position' => $row->getPosition(),
            ];
        }

        return $devices;
    }

    public function getTopKeywords(string $siteUrl, string $pageUrl, array $tokens): array
    {
        $client = new \Google_Client();
        $client->setAccessToken([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'created' => time(),
        ]);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($tokens['refresh_token']);
        }

        $service = new \Google_Service_Webmasters($client);

        $request = new \Google_Service_Webmasters_SearchAnalyticsQueryRequest([
            'startDate' => now()->subDays(28)->toDateString(),
            'endDate' => now()->toDateString(),
            'dimensions' => ['query'],
            'rowLimit' => 20,
            'dimensionFilterGroups' => [[
                'filters' => [[
                    'dimension' => 'page',
                    'operator' => 'equals',
                    'expression' => $pageUrl
                ]]
            ]]
        ]);

        $response = $service->searchanalytics->query($siteUrl, $request);

        $keywords = [];
        foreach ($response->getRows() as $row) {
            $keywords[] = [
                'query' => $row->getKeys()[0],
                'clicks' => $row->getClicks(),
                'impressions' => $row->getImpressions(),
                'ctr' => $row->getCtr(),
                'position' => $row->getPosition(),
            ];
        }

        return $keywords;
    }
}
