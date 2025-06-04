<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoIndexation;
use App\Services\SearchConsoleService;
use App\Services\GoogleAnalyticsService;
use App\Services\CustomCrawlerService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SeoIndexationController extends Controller
{
    public function index(Request $request)
    {
        $data = SeoIndexation::latest()->paginate(25);
        dd(23);
        return response()->json($data);
    }

    public function show(SeoIndexation $seoIndexation)
    {
        return response()->json($seoIndexation);
    }

    public function report(Request $request)
    {
        $url = $request->get('url');
        if (!$url) {
            return response()->json(['error' => 'Missing URL'], 422);
        }

        try {
            $crawler = new CustomCrawlerService();
            $basicData = $crawler->crawlPage($url);

            $jsonLd = $this->validateJsonLd($basicData['headers']['content-type'] ?? '', $basicData['headers'] ?? []);
            $niche = $this->guessNicheFromMeta($basicData['meta_description'] ?? '', $basicData['title'] ?? '');

            return response()->json([
                'url' => $url,
                'data' => $basicData,
                'json_ld_valid' => $jsonLd,
                'detected_niche' => $niche
            ]);
        } catch (\Exception $e) {
            Log::error("SEO Report Error for $url: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function inspect(Request $request)
    {
        $url = $request->get('url');
        $site = $request->get('site');
        $credentials = config('services.google.credentials');
        $accessToken = $request->user()->google_token;

        $service = new SearchConsoleService($credentials, $accessToken);
        $results = $service->inspectUrl($site, $url);

        return response()->json($results);
    }

    private function validateJsonLd($contentType, $headers)
    {
        return str_contains($contentType, 'ld+json') || collect($headers)->keys()->contains(fn($h) => str_contains($h, 'ld+json'));
    }

    private function guessNicheFromMeta($description, $title)
    {
        $combined = strtolower($description . ' ' . $title);

        $niches = [
            'health' => ['fitness', 'diet', 'wellness'],
            'tech' => ['software', 'apps', 'ai', 'technology'],
            'finance' => ['investment', 'stocks', 'money'],
            'travel' => ['trip', 'hotel', 'flight', 'destination'],
            'food' => ['recipe', 'cooking', 'baking', 'dish']
        ];

        foreach ($niches as $niche => $keywords) {
            foreach ($keywords as $word) {
                if (str_contains($combined, $word)) {
                    return ucfirst($niche);
                }
            }
        }

        return 'General';
    }
}
