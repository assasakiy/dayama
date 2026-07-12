<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // In production, store the subscriber or integrate with a service
        \Illuminate\Support\Facades\Log::info('Newsletter subscription', $validated);

        return response()->json([
            'success' => true,
            'message' => 'You\'ve been subscribed successfully.',
        ]);
    }
}
