<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Website;
use App\Models\PageView;
use App\Models\SeoKeyword;
use App\Models\SeoIndexation;
use App\Services\GoogleService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleDataCollection extends Command
{
    protected $signature = 'site:sync-data';

    protected $description = 'Fetch and store analytics, SEO keywords, and indexation data from Google APIs';

    public function handle()
    {
        $this->info("Starting Google data sync for the past year...");

        $users = \App\Models\User::whereHas('providers', function ($q) {
            $q->where('provider', 'google');
        })->get();

        foreach ($users as $user) {
            $provider = $user->providers()->where('provider', 'google')->first();
            if (!$provider) continue;

            $google = new GoogleService([
                'provider_token' => $provider->provider_token,
                'refresh_token' => $provider->refresh_token,
            ]);

            $startDate = now()->subYear()->toDateString();
            $endDate = now()->toDateString();

            $websites = Website::where('user_id', $user->id)->where('disabled', false)->get();
            $properties = collect(json_decode($provider->properties, true));

            foreach ($websites as $site) {
                $matchedProperty = $properties->first(fn($p) => Str::contains(strtolower($p['displayName']), parse_url($site->domain, PHP_URL_HOST)));
                if (!$matchedProperty) continue;

                $propertyId = str_replace('properties/', '', $matchedProperty['name']);

                // ANALYTICS DATA
                $analytics = $google->getDetailedPagesAnalytics($propertyId, $startDate, $endDate);

                foreach ($analytics['rows'] ?? [] as $row) {
                    $page = PageView::updateOrCreate([
                        'website_id' => $site->id,
                        'path' => $row['dimensionValues'][0]['value'],
                        'country' => $row['dimensionValues'][1]['value'] ?? 'US',
                        'source' => $row['dimensionValues'][2]['value'] ?? 'direct',
                        'date' => $startDate,
                    ], [
                        'pageviews' => $row['metricValues'][0]['value'] ?? 0,
                        'sessions' => $row['metricValues'][1]['value'] ?? 0,
                        'users' => $row['metricValues'][2]['value'] ?? 0,
                        'bounce_rate' => $row['metricValues'][3]['value'] ?? 0,
                        'engagement_time' => $row['metricValues'][4]['value'] ?? 0,
                        'events' => $row['metricValues'][5]['value'] ?? 0,
                    ]);

                    // INDEXATION DATA
                    try {
                        $fullUrl = rtrim($site->domain, '/') . $page->path;
                        $inspection = $google->inspectUrl($site->domain, $fullUrl);
                        $result = $inspection['inspectionResult'] ?? [];

                        if (!empty($result)) {
                            $indexResult = $result['indexStatusResult'] ?? [];
                            $mobileResult = $result['mobileUsabilityResult'] ?? [];

                            SeoIndexation::updateOrCreate([
                                'website_id' => $site->id,
                                'url' => $fullUrl,
                            ], [
                                'status' => $indexResult['coverageState'] ?? null,
                                'verdict' => $indexResult['verdict'] ?? null,
                                'robots_state' => $indexResult['robotsTxtState'] ?? null,
                                'indexing_state' => $indexResult['indexingState'] ?? null,
                                'page_fetch_state' => $indexResult['pageFetchState'] ?? null,
                                'crawled_as' => $indexResult['crawledAs'] ?? null,
                                'last_crawled_at' => $indexResult['lastCrawlTime'] ?? null,
                                'google_canonical' => $indexResult['googleCanonical'] ?? null,
                                'user_canonical' => $indexResult['userCanonical'] ?? null,
                                'referring_urls' => json_encode($indexResult['referringUrls'] ?? []),
                                'mobile_usability_verdict' => $mobileResult['verdict'] ?? null,
                                'inspection_link' => $result['inspectionResultLink'] ?? null,
                                'description' => $indexResult['verdict'] ?? null,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning("SEO indexation fetch failed for {$fullUrl}: " . $e->getMessage());
                    }
                }

                // SEO KEYWORDS DATA
                try {
                    $seoData = $google->getSearchAnalytics($site->domain, $startDate, $endDate);

                    foreach ($seoData->getRows() as $row) {
                        SeoKeyword::updateOrCreate([
                            'website_id' => $site->id,
                            'keyword' => $row->getKeys()[1],
                            'page_url' => $row->getKeys()[2],
                            'country' => $row->getKeys()[3] ?? 'ALL',
                            'device' => $row->getKeys()[4] ?? 'ALL',
                            'date' => $row->getKeys()[0],
                        ], [
                            'clicks' => $row->getClicks(),
                            'impressions' => $row->getImpressions(),
                            'ctr' => $row->getCtr(),
                            'position' => $row->getPosition(),
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Search Console error for {$site->domain}: " . $e->getMessage());
                }
            }
        }

        $this->info("Google data sync completed for past year.");
    }
}
