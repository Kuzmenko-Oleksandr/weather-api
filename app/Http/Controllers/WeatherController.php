<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;

class WeatherController extends Controller
{
    /**
     * Fetches weather data for a given city.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeather(Request $request)
    {
        // Validate the request to ensure 'city' is provided
        $request->validate([
            'city' => 'required|string',
        ]);

        $city = $request->get('city');
        $apiKey = env('WEATHER_API_KEY');

        try {
            // Send request to Weather API
            $response = Http::get("http://api.weatherapi.com/v1/current.json", [
                'key' => $apiKey,
                'q' => $city,
            ]);

            // Handle unsuccessful response
            if ($response->failed()) {
                Log::error("Weather API request failed for city: {$city}");
                return response()->json(['message' => 'City not found or API error.'], 404);
            }

            $data = $response->json();

            // Return the weather data in a structured format
            return response()->json([
                'temperature' => $data['current']['temp_c'],
                'humidity'    => $data['current']['humidity'],
                'description' => $data['current']['condition']['text'],
            ]);

        } catch (\Exception $e) {
            Log::error('Weather API Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal server error. Please try again later.'
            ], 500);
        }
    }

    /**
     * Confirms a subscription based on the provided token.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm($token)
    {
        try {
            // Find subscription using the provided token
            $subscription = Subscription::where('token', $token)->first();

            // If token is invalid or not found
            if (!$subscription) {
                return response()->json([
                    'message' => 'Invalid token or subscription not found.'
                ], 404);
            }

            // Update subscription as confirmed
            $subscription->update(['confirmed' => true]);

            return response()->json([
                'message' => 'Subscription confirmed successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Confirmation Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal server error. Please try again later.'
            ], 500);
        }
    }
}
