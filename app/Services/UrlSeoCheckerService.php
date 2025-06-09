<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Iodev\Whois\Whois;
use Google_Client;
use Google_Service_SearchConsole;
use Google_Service_SearchConsole_InspectUrlIndexRequest;

class SeoAuditService
{
    public function runAudit(string $url, array $googleTokens): array
    {
        $html = $this->fetchHtml($url);

        return [
            'pagespeed'       => $this->checkSpeed($url),
            'alt_check'       => $this->checkAltTags($html),
            'semantic_html'   => $this->checkSemanticTags($html),
            'json_ld'         => $this->checkJsonLd($html),
            'domain_age'      => $this->getDomainAge($url),
            'host'            => $this->getHostingProvider($url),
            'google_feedback' => $this->inspectWithGoogle($url, $googleTokens),
        ];
    }

    private function fetchHtml(string $url): string
    {
        return Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url)->body();
    }

    private function checkSpeed(string $url): int
    {
        $response = Http::get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', [
            'url' => $url,
            'key' => config('services.google.api_key'),
        ]);

        return (int) round($response['lighthouseResult']['categories']['performance']['score'] * 100);
    }

    private function checkAltTags(string $html): int
    {
        $crawler = new Crawler($html);
        $images = $crawler->filter('img');
        $missing = 0;

        foreach ($images as $img) {
            if (!$img->hasAttribute('alt') || trim($img->getAttribute('alt')) === '') {
                $missing++;
            }
        }

        return $missing;
    }

    private function checkSemanticTags(string $html): array
    {
        $tags = ['header', 'main', 'article', 'section', 'footer'];
        $counts = [];

        foreach ($tags as $tag) {
            $counts[$tag] = substr_count($html, "<$tag");
        }

        return $counts;
    }

    private function checkJsonLd(string $html): bool
    {
        $crawler = new Crawler($html);
        $jsonLd = $crawler->filterXPath('//script[@type="application/ld+json"]');
        return $jsonLd->count() > 0;
    }

    private function getDomainAge(string $url): ?string
    {
        $domain = parse_url($url, PHP_URL_HOST);
        $whois = Whois::create();
        $info = $whois->loadDomainInfo($domain);
        return $info ? date('Y-m-d', $info->getCreationDate()) : null;
    }

    private function getHostingProvider(string $url): ?string
    {
        $domain = parse_url($url, PHP_URL_HOST);
        $ip = gethostbyname($domain);
        $res = Http::get("https://ipapi.co/{$ip}/json/")->json();
        return $res['org'] ?? null;
    }

    private function inspectWithGoogle(string $url, array $tokens): array
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'created' => time(),
        ]);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($tokens['refresh_token']);
        }

        $searchConsole = new Google_Service_SearchConsole($client);
        $siteUrl = 'sc-domain:' . parse_url($url, PHP_URL_HOST);

        $request = new Google_Service_SearchConsole_InspectUrlIndexRequest([
            'inspectionUrl' => $url,
            'siteUrl' => $siteUrl,
        ]);

        try {
            $response = $searchConsole->urlInspection_index->inspect($request);
            return $response->toSimpleObject();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getIntentFromKeyword(string $keyword): string
    {
        $keyword = strtolower(trim($keyword));

        $informationalPatterns = [
            '/\\bhow to\\b/',
            '/\\bwhat is\\b/',
            '/\\bwhy\\b/',
            '/\\bguide\\b/',
            '/\\btutorial\\b/',
            '/\\bmeaning\\b/',
            '/\\bdefinition\\b/',
            '/\\bexample\\b/',
            '/\\blist\\b/'
        ];

        $transactionalPatterns = [
            '/\\bbuy\\b/',
            '/\\bcheap\\b/',
            '/\\bdiscount\\b/',
            '/\\bprice\\b/',
            '/\\border\\b/',
            '/\\bget\\b/',
            '/\\bshop\\b/',
            '/\\bnear me\\b/',
            '/\\bdeal\\b/',
            '/\\bsale\\b/'
        ];

        $commercialPatterns = [
            '/\\bbest\\b/',
            '/\\btop\\b/',
            '/\\bvs\\b/',
            '/\\breview\\b/',
            '/\\bcomparison\\b/',
            '/\\bcompare\\b/',
            '/\\bratings?\\b/',
            '/\\bfeatures\\b/'
        ];

        $navigationalPatterns = [
            '/\\bfacebook\\b/',
            '/\\btwitter\\b/',
            '/\\byoutube\\b/',
            '/\\blogin\\b/',
            '/\\bsign up\\b/',
            '/\\baccount\\b/',
            '/\\bapp\\b/',
            '/\\bwebsite\\b/'
        ];

        foreach ($informationalPatterns as $pattern) {
            if (preg_match($pattern, $keyword)) return 'Informational';
        }

        foreach ($transactionalPatterns as $pattern) {
            if (preg_match($pattern, $keyword)) return 'Transactional';
        }

        foreach ($commercialPatterns as $pattern) {
            if (preg_match($pattern, $keyword)) return 'Commercial Investigation';
        }

        foreach ($navigationalPatterns as $pattern) {
            if (preg_match($pattern, $keyword)) return 'Navigational';
        }

        return 'Unclassified';
    }
}
