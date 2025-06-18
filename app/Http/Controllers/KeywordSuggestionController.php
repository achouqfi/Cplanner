<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KeywordSuggestionService;

class KeywordSuggestionController extends Controller
{
    protected KeywordSuggestionService $service;

    public function __construct(KeywordSuggestionService $service)
    {
        $this->service = $service;
    }

    public function analyze(string $keyword)
    {
        $result = $this->service->analyze($keyword);

        dd(
           $result 
        );

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
