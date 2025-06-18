<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KeywordSuggestionService
{
    public function analyze(string $keyword): array
    {
        $volumeData = $this->getVolumeCPCAndKD($keyword);
        $variants = $this->getAutosuggestVariants($keyword);
        $questions = $this->extractQuestions($variants);


        return [
            'keyword' => $keyword,
            'volume' => $volumeData['volume'] ?? null,
            'kd' => $volumeData['kd'] ?? null,
            'cpc' => $volumeData['cpc'] ?? null,
            'last_updated' => now()->toDateString(),
            'variants' => $variants,
            'questions' => $questions,
        ];
    }

    private function getVolumeCPCAndKD(string $keyword): array
    {
            $response = Http::withHeaders([
                'X-RapidAPI-Host' => 'keyword-surfer-api.p.rapidapi.com',
                'X-RapidAPI-Key' => config('services.rapidapi.key'),
            ])->get('https://keyword-surfer-api.p.rapidapi.com/search-volume', [
                'keyword' => $keyword,
                'country' => 'us'
            ]);

            $data = $response->json();

            return [
                'volume' => $data['search_volume'] ?? null,
                'cpc' => $data['cpc'] ?? null,
                'kd' => $data['difficulty'] ?? null, // May not be provided, fallback optional
            ];

    }

    private function getAutosuggestVariants(string $keyword): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0'
            ])->get('https://suggestqueries.google.com/complete/search', [
                'client' => 'firefox',
                'q' => $keyword
            ]);

            return $response->json()[1] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function extractQuestions(array $variants): array
    {
        return array_filter($variants, function ($variant) {
            return preg_match('/^(who|what|when|where|why|how)\b/i', $variant);
        });
    }
}
