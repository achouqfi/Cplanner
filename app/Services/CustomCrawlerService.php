<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class CustomCrawlerService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(["timeout" => 10]);
    }

    public function crawlPage(string $url): array
    {
        try {
            $response = $this->client->get($url);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $title = $crawler->filter('title')->count() ? $crawler->filter('title')->text('') : '';
            $metaDescription = $crawler->filterXPath("//meta[@name='description']")->count()
                ? $crawler->filterXPath("//meta[@name='description']")->attr('content')
                : '';
            $canonical = $crawler->filterXPath("//link[@rel='canonical']")->count()
                ? $crawler->filterXPath("//link[@rel='canonical']")->attr('href')
                : '';

            return [
                'title' => $title,
                'meta_description' => $metaDescription,
                'canonical' => $canonical,
                'h1' => $crawler->filter('h1')->each(fn($node) => $node->text()),
                'internal_links' => $crawler->filter('a')->each(function ($node) {
                    $href = $node->attr('href');
                    return $href && str_starts_with($href, '/') ? $href : null;
                }),
                'headers' => $response->getHeaders(),
                'status_code' => $response->getStatusCode(),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
