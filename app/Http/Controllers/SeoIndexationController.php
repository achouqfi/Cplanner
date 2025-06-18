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
    }

    public function show(SeoIndexation $seoIndexation)
    {
    }

    public function report(Request $request)
    {
    }

    public function inspect(Request $request)
    {
    }

}
