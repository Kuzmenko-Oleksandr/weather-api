<?php

use App\Http\Controllers\WeatherController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;


Route::get('/', function () {
    return view('weather');
});

Route::get('/weather', function () {
    return view('weather');
});

Route::get('/confirmation', function () {
    return view('confirmation');
});

Route::get('/api/confirm/{token}', [SubscriptionController::class, 'confirm']);


Route::get('/get-cities', function () {
    // Get the query parameter from the request
    $query = request('query');

    // If the query length is less than 2 characters, return an empty array
    if (strlen($query) < 2) {
        return response()->json([]);
    }

    // Weather API key from the environment file
    $apiKey = env('WEATHER_API_KEY');
    $url = "http://api.weatherapi.com/v1/search.json";

    try {
        // API request to fetch city suggestions
        $response = Http::get($url, [
            'key' => $apiKey,
            'q' => $query
        ]);

        // Check if the response is successful
        if ($response->successful()) {
            $cities = $response->json();

            // Return the fetched cities
            return response()->json($cities);
        }

        // Return an error response if the API request fails
        return response()->json(['message' => 'Failed to fetch cities.'], 500);

    } catch (\Exception $e) {
        // Log the exception and return an error response
        Log::error("Error fetching cities: " . $e->getMessage());
        return response()->json(['message' => 'Server error.'], 500);
    }
});

Route::get('/unsubscribe', function () {
    return view('unsubscribe');
});

Route::post('/api/unsubscribe', [SubscriptionController::class, 'unsubscribe']);
