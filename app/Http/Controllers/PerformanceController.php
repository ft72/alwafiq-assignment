<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class PerformanceController extends Controller
{
    public function showTestForm()
    {
        return Inertia::render('PerformanceTest');
    }

    public function runTest(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'platform' => 'required|in:Mobile,Desktop',
        ]);

        $url = $validated['url'];
        $platform = strtolower($validated['platform']); // 'mobile' or 'desktop'

        $apiEndpoint = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

        $apiKey = env('GOOGLE_PAGESPEED_API_KEY');

        if (!$apiKey) {
            return Inertia::render('PerformanceTest', [
                'apiError' => 'Google Pagespeed API key is not configured.'
            ]);
        }

        try {
            $verifySSL = env('APP_ENV') !== 'local'; // Disable in local development

            $response = Http::withOptions([
                'verify' => $verifySSL,
            ])->get($apiEndpoint, [
                'url' => $url,
                'category' => 'performance',
                'strategy' => $platform,
                'key' => $apiKey,
            ]);

            if ($response->failed()) {
                return Inertia::render('PerformanceTest', [
                    'apiError' => 'Failed to retrieve performance data.',
                    'errorDetails' => $response->json(),
                ]);
            }

            $data = $response->json();
            $performanceScore = $data['lighthouseResult']['categories']['performance']['score'] ?? null;

            if (is_null($performanceScore)) {
                return Inertia::render('PerformanceTest', [
                    'apiError' => 'Performance score not found in the API response.',
                ]);
            }

            $performanceScorePercentage = $performanceScore * 100;

            return Inertia::render('PerformanceTest', [
                'performanceScore' => $performanceScorePercentage,
                'testedUrl' => $url,
                'platform' => ucfirst($platform),
            ]);
        } catch (\Exception $e) {
            \Log::error('Lighthouse Test Failed: ' . $e->getMessage());

            return Inertia::render('PerformanceTest', [
                'apiError' => 'An unexpected error occurred while processing the performance test.',
                'errorDetails' => $e->getMessage(),
            ]);
        }
    }
}
