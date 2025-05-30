<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWebsiteRequest;
use App\Models\Website;
use Illuminate\Http\Request;
use App\Services\GoogleService;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function fetchAnalytics()
    {
        $period = request('period', 'thisMonth');
        $user = auth()->user();
        $provider = $user->providers()->where('provider', 'google')->first();


        switch ($period) {
            case 'today':
                $startDate = now()->toDateString();
                $endDate = now()->toDateString();
                break;
            case 'thisWeek':
                $startDate = now()->startOfWeek()->toDateString();
                $endDate = now()->toDateString();
                break;
            case 'lastWeek':
                $startDate = now()->subWeek()->startOfWeek()->toDateString();
                $endDate = now()->subWeek()->endOfWeek()->toDateString();
                break;
            case 'thisMonth':
                $startDate = now()->startOfMonth()->toDateString();
                $endDate = now()->toDateString();
                break;
            case 'lastMonth':
                $startDate = now()->subMonth()->startOfMonth()->toDateString();
                $endDate = now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'last3Months':
                $startDate = now()->subMonths(3)->startOfMonth()->toDateString();
                $endDate = now()->toDateString();
                break;
            default:
                // Default to last 7 days
                $startDate = now()->subDays(6)->toDateString();
                $endDate = now()->toDateString();
        }



        if (!$provider || !$provider->provider_token) {
            return response()->json(['error' => 'Google account not connected.'], 403);
        }

        $google = new GoogleService([
            'provider_token' => $provider->provider_token,
            'refresh_token' => $provider->refresh_token,
        ]);

        $websites = Website::where('user_id', $user->id)->where('disabled', false)->get();

        $properties = collect(json_decode($provider->properties, true));

        $domains = $websites->map(function ($site) use ($google, $startDate, $endDate, $properties) {
            $matchedProperty = $properties->first(fn($p) => str_contains(strtolower($p['displayName']), parse_url($site->domain, PHP_URL_HOST)));

            if (!$matchedProperty) return null;

            $propertyId = str_replace('properties/', '', $matchedProperty['name']);

            try {
                // Traffic (sessions per day)
                $dailyTraffic = $google->getDailyAnalyticsReport($propertyId, $startDate, $endDate)->json('rows', []);

                $trafficTrend = collect($dailyTraffic)->map(fn($r) => (int) $r['metricValues'][0]['value'])->all();

                $trafficCurrent = collect($trafficTrend)->sum();

                $trafficPrev = $trafficTrend[count($trafficTrend) - 2] ?? $trafficCurrent;
                $trafficGrowth = $trafficPrev > 0 ? (($trafficCurrent - $trafficPrev) / $trafficPrev) * 100 : 0;

                // SEO (Search Console)
                $searchAnalytics = $google->getSearchAnalytics($site->domain, $startDate, $endDate)->getRows() ?? [];


                $seoTrend = collect($searchAnalytics)->groupBy(fn($r) => $r->getKeys()[0])
                    ->map(fn($rows) => collect($rows)->sum(fn($r) => $r->getClicks()))
                    ->values()->all();


                $seoCurrent = collect($seoTrend)->sum();
                $seoPrev = $seoTrend[count($seoTrend) - 2] ?? $seoCurrent;
                $seoGrowth = $seoPrev > 0 ? (($seoCurrent - $seoPrev) / $seoPrev) * 100 : 0;

                $clicks = collect($searchAnalytics)->sum(fn($r) => $r->getClicks());
                $impressions = collect($searchAnalytics)->sum(fn($r) => $r->getImpressions());
                $ctr = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;

                return [
                    'id' => $site->id,
                    'name' => $site->domain,
                    'created_at' => $site->created_at->toDateString(),
                    'lastUpdated' => $site->updated_at->toDateString(),
                    'status' => 'active',
                    'traffic' => [
                        'current' => $trafficCurrent,
                        'growth' => round($trafficGrowth, 1),
                        'trend' => $trafficTrend,
                    ],
                    'seo' => [
                        'clicks' => $clicks,
                        'impressions' => $impressions,
                        'ctr' => round($ctr, 1),
                        'growth' => round($seoGrowth, 1),
                        'trend' => $seoTrend,
                    ],
                ];
            } catch (\Throwable $e) {
                return null;
            }
        })->filter()->values();

        $sorted = $domains->sortByDesc(fn($d) => $d['traffic']['current'])->values();

        return response()->json([
            'filteredDomains' => $domains,
            'sortedDomains' => $sorted,
        ]);
    }


    public function index()
    {
        $user = auth()->user();
        $provider = $user->providers()->where('provider', 'google')->first();

        if (!$provider || !$provider->provider_token) {
            return response()->json(['error' => 'Google account not connected.'], 403);
        }

        $google = new GoogleService([
            'provider_token' => $provider->provider_token,
            'refresh_token' => $provider->refresh_token,
        ]);

        $startDate = now()->subDays(30)->toDateString();
        $endDate = now()->toDateString();

        $websites = Website::where('user_id', $user->id)
            ->where('disabled', false)
            ->get(['id', 'name', 'domain', 'created_at']);

        $enrichedWebsites = $websites->map(function ($site) use ($google, $startDate, $endDate, $provider) {
            $seoStats = [
                'clicks' => 0,
                'impressions' => 0,
                'avg_ctr' => '0%',
                'avg_position' => 0,
                'daily' => [],
            ];

            $trafficStats = [
                'total_sessions' => 0,
                'sources' => [],
                'daily' => [],
            ];

            try {
                $searchData = $google->getSearchAnalytics($site->domain, $startDate, $endDate);
                $rows = collect($searchData->getRows() ?? []);

                $groupedByDate = $rows->groupBy(fn($row) => $row->getKeys()[0]);

                $seoStats['daily'] = $groupedByDate->map(function ($group) {
                    $clicks = collect($group)->sum(fn($r) => $r->getClicks());
                    $impressions = collect($group)->sum(fn($r) => $r->getImpressions());
                    $ctr = collect($group)->avg(fn($r) => $r->getCtr()) * 100;
                    $position = collect($group)->avg(fn($r) => $r->getPosition());
                    return [
                        'clicks' => $clicks,
                        'impressions' => $impressions,
                        'avg_ctr' => round($ctr, 2) . '%',
                        'avg_position' => round($position, 2),
                    ];
                });

                $aggregated = $rows->reduce(function ($carry, $row) {
                    $carry['clicks'] += $row->getClicks();
                    $carry['impressions'] += $row->getImpressions();
                    $carry['ctr'] += $row->getCtr();
                    $carry['position'] += $row->getPosition();
                    $carry['count']++;
                    return $carry;
                }, ['clicks' => 0, 'impressions' => 0, 'ctr' => 0, 'position' => 0, 'count' => 0]);

                if ($aggregated['count']) {
                    $seoStats['clicks'] = $aggregated['clicks'];
                    $seoStats['impressions'] = $aggregated['impressions'];
                    $seoStats['avg_ctr'] = round(($aggregated['ctr'] / $aggregated['count']) * 100, 2) . '%';
                    $seoStats['avg_position'] = round($aggregated['position'] / $aggregated['count'], 2);
                }
            } catch (\Exception $e) {
            }

            try {
                $properties = json_decode($provider->properties, true);
                $matching = collect($properties)->first(function ($property) use ($site) {
                    return str_contains(strtolower($property['displayName']), parse_url($site->domain, PHP_URL_HOST));
                });

                if ($matching) {
                    $propertyId = str_replace('properties/', '', $matching['name']);

                    // Source breakdown
                    $report = $google->getAnalyticsReport($propertyId, $startDate, $endDate);
                    $rows = collect($report->json('rows', []));
                    $trafficStats['sources'] = $rows->mapWithKeys(fn($row) => [
                        $row['dimensionValues'][0]['value'] => (int) $row['metricValues'][0]['value']
                    ]);
                    $trafficStats['total_sessions'] = $trafficStats['sources']->sum();

                    // Daily traffic breakdown
                    $dailyReport = $google->getDailyAnalyticsReport($propertyId, $startDate, $endDate);
                    $dailyRows = collect($dailyReport->json('rows', []));

                    $trafficStats['daily'] = $dailyRows->mapWithKeys(fn($row) => [
                        $row['dimensionValues'][0]['value'] => (int) $row['metricValues'][0]['value']
                    ]);


                    //top pages
                    $getTopPages = $google->getTopPages($propertyId, $startDate, $endDate);
                    $topPagesRows = collect($getTopPages->json('rows', []));

                    $trafficStats['topPages'] = $topPagesRows->map(function ($row) {
                        return [
                            'page' => $row['dimensionValues'][0]['value'],
                            'views' => (int) $row['metricValues'][0]['value'],
                        ];
                    });
                }
            } catch (\Exception $e) {
                $trafficStats['error'] = 'Failed to fetch GA4 data';
            }

            return [
                'id' => $site->id,
                'name' => $site->name,
                'domain' => $site->domain,
                'created_at' => $site->created_at,
                'seo' => $seoStats,
                'traffic' => $trafficStats,
            ];
        });

        return response()->json([
            'websites' => $enrichedWebsites,
            'summary' => [
                'total_sites' => $websites->count(),
                'total_clicks' => $enrichedWebsites->sum(fn($w) => $w['seo']['clicks']),
                'total_sessions' => $enrichedWebsites->sum(fn($w) => $w['traffic']['total_sessions'] ?? 0),
            ]
        ]);
    }


    public function access_websites()
    {
        $websites = Website::where('user_id', auth()->id())
            ->where('disabled', false)
            ->orWhere('is_active', false)
            ->get(['id', 'name', 'domain']);

        return response()->json([
            'websites' => $websites
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function addWebsite(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:websites,id'],
        ]);

        $website = Website::where('user_id', auth()->id())->findOrFail($request->id);

        $website->update([
            'disabled' => false,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Website added and activated successfully.',
            'website' => $website,
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Website $website)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Website $website)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWebsiteRequest $request, Website $website)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Website $website)
    {
        //
    }
}
