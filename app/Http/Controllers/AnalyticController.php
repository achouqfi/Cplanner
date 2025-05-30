<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Services\GoogleService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AnalyticController extends Controller
{
    public function pageAnalytics(Request $request)
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

        $endDate = now()->toDateString();
        $countryFilter = $request->input('country');
        $searchQuery = $request->input('search');
        $sortBy = $request->input('sort', 'pageviews');
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        $dateRange = (int) $request->input('date_range', 30);
        $startDate = now()->subDays($dateRange)->toDateString();

        $websites = Website::where('user_id', $user->id)->where('disabled', false)->get();
        $properties = collect(json_decode($provider->properties, true));

        $allPages = [];

        foreach ($websites as $site) {
            $matchedProperty = $properties->first(fn($p) =>
                Str::contains(strtolower($p['displayName']), parse_url($site->domain, PHP_URL_HOST))
            );

            if (!$matchedProperty) continue;

            $propertyId = str_replace('properties/', '', $matchedProperty['name']);
            $report = $google->getDetailedPagesAnalytics($propertyId, $startDate, $endDate, $perPage);

           
            $pages = collect($report['rows'] ?? [])->groupBy(fn($row) => $row['dimensionValues'][0]['value'] ?? '/')
                ->map(function ($group) use ($site) {
                    $totals = [
                        'pageviews' => 0,
                        'sessions' => 0,
                        'users' => 0,
                        'events' => 0,
                        'bounceRate' => 0,
                        'avgTime' => 0,
                        'topCountries' => [],
                        'trafficSource' => [],
                        'engagementRate' => 0,
                    ];

                    foreach ($group as $row) {
                        $totals['pageviews'] += (int) ($row['metricValues'][0]['value'] ?? 0);
                        $totals['sessions'] += (int) ($row['metricValues'][1]['value'] ?? 0);
                        $totals['users'] += (int) ($row['metricValues'][2]['value'] ?? 0);
                        $totals['bounceRate'] += (float) ($row['metricValues'][3]['value'] ?? 0);
                        $totals['avgTime'] += (float) ($row['metricValues'][4]['value'] ?? 0);
                        $totals['events'] += (int) ($row['metricValues'][5]['value'] ?? 0);
                        $totals['engagementRate'] += (float) ($row['metricValues'][6]['value'] ?? 0);

                        $country = $row['dimensionValues'][1]['value'] ?? 'US';
                        $totals['topCountries'][$country] = ($totals['topCountries'][$country] ?? 0) + ((int) ($row['metricValues'][2]['value'] ?? 0));
                    }

                    $avgSeconds = $totals['avgTime'] > 0 && count($group) > 0
                        ? (int) ($totals['avgTime'] / count($group))
                        : 0;

                    $topCountries = collect($totals['topCountries'])->sortDesc()->take(3)->map(function ($users, $code) {
                        return [
                            'code' => $code,
                            'flag' => $this->countryToFlag($code),
                            'users' => $users
                        ];
                    })->values();

                    return [
                        'site' => $site->domain,
                        'path' => $group->first()['dimensionValues'][0]['value'],
                        'category' => 'Auto',
                        'pageviews' => $totals['pageviews'],
                        'sessions' => $totals['sessions'],
                        'users' => $totals['users'],
                        'events' => $totals['events'],
                        'bounceRate' => round($totals['bounceRate'] / count($group), 2),
                        'avgTime' => $this->formatSeconds($avgSeconds),
                        'topCountries' => $topCountries,
                        'engagementRate' => round($totals['engagementRate'] / count($group), 2),
                    ];
                })->values();

            $allPages = array_merge($allPages, $pages->toArray());
        }

        $filtered = collect($allPages)->filter(function ($page) use ($countryFilter, $searchQuery) {
            $match = true;

            if ($countryFilter && $countryFilter !== 'all') {
                $match = collect($page['topCountries'])->pluck('code')->contains($countryFilter);
            }

            if ($searchQuery) {
                $match = $match && (Str::contains(strtolower($page['path']), strtolower($searchQuery)) ||
                    Str::contains(strtolower($page['category']), strtolower($searchQuery)));
            }

            return $match;
        });

        $sorted = $filtered->sortByDesc($sortBy)->values();

        $paginated = $sorted->forPage($page, $perPage)->values();

        return response()->json([
            'data' => $paginated,
            'meta' => [
                'total' => $sorted->count(),
                'page' => $page,
                'per_page' => $perPage,
            ]
        ]);
    }

    function formatSeconds($seconds)
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%02dm %02ds', $minutes, $remainingSeconds);
    }

    public function countryToFlag(string $countryCode): string
    {
        $code = strtoupper($countryCode);
        if (strlen($code) !== 2) return '🏳️';

        return implode('', array_map(function ($char) {
            return mb_convert_encoding('&#' . (127397 + ord($char)) . ';', 'UTF-8', 'HTML-ENTITIES');
        }, str_split($code)));
    }
}
