<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Handles the subscription creation process.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscriptions',
            'city' => 'required|string',
            'frequency' => 'required|in:hourly,daily',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $token = Str::random(32);

            // Create new subscription record
            Subscription::create([
                'email' => $request->email,
                'city' => $request->city,
                'frequency' => $request->frequency,
                'token' => $token,
                'confirmed' => false,
            ]);

            // Send confirmation email
            Mail::raw("Click to confirm your subscription: http://localhost:8000/api/confirm/{$token}", function ($message) use ($request) {
                $message->to($request->email)->subject('Confirm your subscription');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Subscription created. Check your email to confirm.'
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error. Please try again later.'
            ], 500);
        }
    }

    /**
     * Confirms a subscription using the token.
     *
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm($token)
    {
        // Find subscription by token
        $subscription = Subscription::where('token', $token)->first();

        // If the token is invalid
        if (!$subscription) {
            return redirect('/confirmation')->with([
                'status' => 'error',
                'message' => 'Invalid token.'
            ]);
        }

        // If the subscription is already confirmed
        if ($subscription->confirmed) {
            return redirect('/confirmation')->with([
                'status' => 'info',
                'message' => 'You have already confirmed your subscription!'
            ]);
        }

        // Update subscription as confirmed
        $subscription->update(['confirmed' => true]);

        return redirect('/confirmation')->with([
            'status' => 'success',
            'message' => 'You have successfully subscribed to weather updates!'
        ]);
    }

    /**
     * Unsubscribes a user using the token.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe($token)
    {
        // Find subscription by token
        $subscription = Subscription::where('token', $token)->first();

        // If the token is invalid
        if (!$subscription) {
            return response()->json(['message' => 'Invalid token.'], 404);
        }

        // Delete subscription record
        $subscription->delete();

        return response()->json(['message' => 'Successfully unsubscribed.']);
    }

    /**
     * Retrieves the token associated with the provided email.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTokenByEmail(Request $request)
    {
        // Validate email input
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        // Find subscription by email
        $subscription = Subscription::where('email', $email)->first();

        // If no subscription is found
        if (!$subscription) {
            return response()->json(['message' => 'No subscription found with this email.'], 404);
        }

        // Return the associated token
        return response()->json([
            'token' => $subscription->token
        ]);
    }
}
