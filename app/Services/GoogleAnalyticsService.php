<?php


namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;

class GoogleAnalyticsService
{
    protected $client;

    public function __construct(string $credentialsPath)
    {
        $this->client = new BetaAnalyticsDataClient([
            'credentials' => $credentialsPath,
        ]);
    }

    public function getPageAnalytics(string $propertyId, string $startDate, string $endDate)
    {
        $response = $this->client->runReport([
            'property' => "properties/{$propertyId}",
            'dateRanges' => [
                new DateRange(['start_date' => $startDate, 'end_date' => $endDate]),
            ],
            'dimensions' => [
                new Dimension(['name' => 'pagePath']),
                new Dimension(['name' => 'country']),
                new Dimension(['name' => 'sessionSourceMedium']),
            ],
            'metrics' => [
                new Metric(['name' => 'screenPageViews']),
                new Metric(['name' => 'sessions']),
                new Metric(['name' => 'totalUsers']),
                new Metric(['name' => 'bounceRate']),
                new Metric(['name' => 'userEngagementDuration']),
                new Metric(['name' => 'eventCount']),
            ],
        ]);

        return $response->getRows();
    }
}
