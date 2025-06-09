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
}
