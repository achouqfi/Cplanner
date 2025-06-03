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

            return [
                'title' => $crawler->filter('title')->text(''),
                'meta_description' => $crawler->filterXPath("//meta[@name='description']")->attr('content') ?? '',
                'canonical' => $crawler->filterXPath("//link[@rel='canonical']")->attr('href') ?? '',
                'h1' => $crawler->filter('h1')->each(fn($node) => $node->text()),
                'internal_links' => $crawler->filter('a')->each(function ($node) {
                    $href = $node->attr('href');
                    return str_starts_with($href, '/') ? $href : null;
                }),
                'headers' => $response->getHeaders(),
                'status_code' => $response->getStatusCode(),
            ];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
