<?php

namespace App\Http\Controllers;

use App\Services\NewsletterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    public function __construct(
        private NewsletterService $newsletterService
    ) {}

    public function subscribe(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'name' => 'nullable|string|max:255'
            ]);

            // Check if already subscribed
            if ($this->newsletterService->isSubscribed($validated['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already subscribed to our newsletter.'
                ], 422);
            }

            $subscription = $this->newsletterService->subscribe(
                $validated['email'],
                $validated['name'] ?? null,
                'website'
            );

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed! Please check your email for confirmation.'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid email address.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Newsletter subscription error', [
                'error' => $e->getMessage(),
                'email' => $request->input('email')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again later.'
            ], 500);
        }
    }

    public function unsubscribe(Request $request, string $token)
    {
        $success = $this->newsletterService->unsubscribe($token);

        if ($success) {
            return view('components.newsletter.unsubscribe')->with('success', true);
        }

        return view('components.newsletter.unsubscribe')->with('success', false);
    }
}
