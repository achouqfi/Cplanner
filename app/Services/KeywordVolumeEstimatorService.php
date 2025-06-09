<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KeywordVolumeEstimatorService
{
    public function estimate(string $keyword): array
    {
        return [
            'keyword' => $keyword,
            'google_trends_index' => $this->getGoogleTrendsIndex($keyword),
            'autosuggest_count' => $this->countGoogleAutosuggest($keyword),
            'related_keywords' => $this->getRelatedFromSuggest($keyword),
            'confidence' => 'Low', // or Medium/High based on scoring logic
        ];
    }

    private function getGoogleTrendsIndex(string $keyword): int
    {
        // Free Google Trends index (0 to 100)
        $response = Http::get("https://trends.google.com/trends/api/explore", [
            'hl' => 'en-US',
            'tz' => -120,
            'req' => json_encode([
                'comparisonItem' => [['keyword' => $keyword, 'geo' => '', 'time' => 'today 12-m']],
                'category' => 0,
                'property' => ''
            ]),
        ]);

        // NOTE: Google Trends response is weird. This part needs JSON cleanup (wrap if needed)
        return rand(10, 80); // Placeholder: real response needs processing
    }

    private function countGoogleAutosuggest(string $keyword): int
    {
        $variants = $this->getRelatedFromSuggest($keyword);
        return count($variants);
    }

    private function getRelatedFromSuggest(string $keyword): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0'
        ])->get('http://suggestqueries.google.com/complete/search', [
            'client' => 'firefox',
            'q' => $keyword
        ]);

        return $response->json()[1] ?? [];
    }
}
