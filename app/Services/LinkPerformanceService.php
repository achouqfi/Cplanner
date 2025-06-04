<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class LinkPerformanceService
{
    public function analyze(string $url): array
    {
        try {
            $domain = parse_url($url, PHP_URL_HOST);
            $ip = gethostbyname($domain);
            $headers = ['User-Agent' => 'Mozilla/5.0'];

            $start = microtime(true);
            $response = Http::withHeaders($headers)->timeout(40)->get($url);
            $html = $response->body();
            $loadTime = microtime(true) - $start;

            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->loadHTML($html);
            $xpath = new DOMXPath($doc);

            $text = strip_tags($html);
            $readability = $this->fleschReadingEase($text);

            $images = $doc->getElementsByTagName('img')->length;
            $scripts = $doc->getElementsByTagName('script')->length;
            $links = $doc->getElementsByTagName('a')->length;

            $performanceScore = max(0, min(100, 100 - ($loadTime * 10 + $scripts * 0.5)));
            $structureScore = max(0, min(100, 100 - (strlen($html) / 50000)));
            $grade = $this->gradeFromScore($performanceScore);

            $tech = $this->extractTechnologies($doc, $response->headers());
            $tech['hosting_guess'] = $this->guessHostingProvider($ip);
            $monetization = $this->detectMonetization($doc);
            $jsonLd = $this->extractJsonLd($doc);

            return [
                'url' => $url,
                'ip_address' => $ip,
                'domain' => $domain,
                'load_time_seconds' => round($loadTime, 2),
                'content_length' => strlen($text),
                'readability_score' => $readability,
                'performance_score' => $performanceScore,
                'structure_score' => $structureScore,
                'gtmetrix_grade' => $grade,
                'web_vitals' => [
                    'largest_contentful_paint' => round(min($loadTime * 0.75, 3), 2) . 's',
                    'total_blocking_time' => round(min($scripts * 15 + 100, 600), 0) . 'ms',
                    'cumulative_layout_shift' => round($images * 0.01, 2)
                ],
                'html_elements' => compact('images', 'scripts', 'links'),
                'technologies' => $tech,
                'monetization' => $monetization,
                'json_ld_schemas' => $jsonLd,
                'recommendations' => $this->generateRecommendations([
                    'load_time_seconds' => $loadTime,
                    'readability_score' => $readability,
                    'performance_score' => $performanceScore,
                    'structure_score' => $structureScore,
                    'monetization' => $monetization,
                    'technologies' => $tech
                ]),
                'langchain_summary' => null,
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function gradeFromScore($score)
    {
        return $score >= 90 ? 'A' : ($score >= 80 ? 'B' : ($score >= 70 ? 'C' : 'D'));
    }

    protected function fleschReadingEase($text)
    {
        $sentences = max(1, substr_count($text, '.') + substr_count($text, '!') + substr_count($text, '?'));
        $words = str_word_count($text);
        $syllables = preg_match_all('/[aeiouy]+/i', $text);
        return round(206.835 - 1.015 * ($words / $sentences) - 84.6 * ($syllables / $words), 2);
    }


    protected function extractTechnologies(DOMDocument $doc, array $headers): array
    {
        $xpath = new DOMXPath($doc);

        $tech = [
            'server' => $headers['Server'][0] ?? 'Unknown',
            'x_powered_by' => $headers['X-Powered-By'][0] ?? 'Unknown',
            'cms' => 'Unknown',
            'theme' => 'Unknown',
            'css_frameworks' => [],
            'js_libraries' => [],
            'fonts' => [],
            'analytics' => []
        ];

        // CMS Detection
        $generator = $xpath->query("//meta[@name='generator']")->item(0);
        if ($generator && str_contains(strtolower($generator->getAttribute('content')), 'wordpress')) {
            $tech['cms'] = 'WordPress';
        }

        $nodes = $xpath->query("//link[@href] | //script[@src] | //style[@href]");
        foreach ($nodes as $node) {
            $src = $node->getAttribute('href') ?: $node->getAttribute('src');

            if (strpos($src, 'themes/') !== false && preg_match('#themes/([^/]+)/#', $src, $m)) {
                $tech['theme'] = $m[1];
            }
            if (strpos($src, 'bootstrap') !== false) {
                $tech['css_frameworks'][] = 'Bootstrap';
            }
            if (strpos($src, 'tailwind') !== false) {
                $tech['css_frameworks'][] = 'Tailwind';
            }
            if (strpos($src, 'jquery') !== false) {
                $tech['js_libraries'][] = 'jQuery';
            }
            if (strpos($src, 'vue') !== false) {
                $tech['js_libraries'][] = 'Vue.js';
            }
            if (strpos($src, 'react') !== false) {
                $tech['js_libraries'][] = 'React';
            }
            if (strpos($src, 'fonts.googleapis.com') !== false) {
                $tech['fonts'][] = 'Google Fonts';
            }
            if (strpos($src, 'gtag') !== false || strpos($src, 'analytics') !== false) {
                $tech['analytics'][] = 'Google Analytics';
            }
        }

        return $tech;
    }

    protected function guessHostingProvider(string $ip): string
    {
        try {
            $host = strtolower(gethostbyaddr($ip));
            $map = [
                'hostinger' => 'Hostinger',
                'digitalocean' => 'DigitalOcean',
                'cloudflare' => 'Cloudflare',
                'bluehost' => 'Bluehost',
                'ovh' => 'OVH',
                'namecheap' => 'Namecheap',
                'amazonaws' => 'Amazon AWS',
            ];

            foreach ($map as $keyword => $provider) {
                if (str_contains($host, $keyword)) {
                    return $provider;
                }
            }

            return $host;
        } catch (\Exception) {
            return 'Unknown';
        }
    }

    protected function detectMonetization(DOMDocument $doc): array
    {
        $xpath = new DOMXPath($doc);
        $monetization = [
            'adsense' => false,
            'ezoic' => false,
            'mediavine' => false,
            'amazon_affiliates' => false,
            'taboola' => false,
            'outbrain' => false,
            'revcontent' => false,
            'native_ads' => false,
            'other_networks' => []
        ];

        $scripts = $xpath->query("//script[@src]");
        foreach ($scripts as $script) {
            $src = $script->getAttribute('src');
            if (str_contains($src, 'adsbygoogle')) $monetization['adsense'] = true;
            elseif (str_contains($src, 'ezoic')) $monetization['ezoic'] = true;
            elseif (str_contains($src, 'mediavine')) $monetization['mediavine'] = true;
            elseif (str_contains($src, 'taboola')) $monetization['taboola'] = true;
            elseif (str_contains($src, 'outbrain')) $monetization['outbrain'] = true;
            elseif (str_contains($src, 'revcontent')) $monetization['revcontent'] = true;
            elseif (str_contains($src, 'nativeads')) $monetization['native_ads'] = true;
            elseif (str_contains($src, 'ads') || str_contains($src, 'adserver')) {
                $monetization['other_networks'][] = $src;
            }
        }

        $html = $doc->saveHTML();
        if (str_contains($html, 'amazon.') && (str_contains($html, 'tag=') || str_contains($html, 'ref='))) {
            $monetization['amazon_affiliates'] = true;
        }

        return $monetization;
    }

    protected function extractJsonLd(DOMDocument $doc): array
    {
        $xpath = new DOMXPath($doc);
        $schemas = [];
        $jsonNodes = $xpath->query("//script[@type='application/ld+json']");

        foreach ($jsonNodes as $node) {
            $json = $node->nodeValue;
            $decoded = json_decode($json, true);
            if ($decoded) {
                $schemas[] = $decoded;
            }
        }

        return $schemas;
    }

    private function generateRecommendations(array $data): array
    {
        $tips = [];

        if ($data['performance_score'] < 70) {
            $tips[] = "Optimize images and compress JS/CSS for faster loading.";
        }

        if ($data['load_time_seconds'] > 3) {
            $tips[] = "Use caching/CDN to improve response time.";
        }

        if ($data['readability_score'] < 50) {
            $tips[] = "Simplify content for better user understanding.";
        }

        if ($data['structure_score'] < 80) {
            $tips[] = "Avoid excessive inline styles and optimize HTML structure.";
        }

        if (!collect($data['monetization'])->contains(true)) {
            $tips[] = "Consider monetizing with Google AdSense or affiliate programs.";
        }

        if ($data['technologies']['cms'] === 'Unknown') {
            $tips[] = "Use a CMS for better content management.";
        }

        if (empty($data['technologies']['fonts'])) {
            $tips[] = "Use web fonts for improved readability and design consistency.";
        }

        return $tips ?: ["Site is well optimized — keep monitoring for changes!"];
    }
}
