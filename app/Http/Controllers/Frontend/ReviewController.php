<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // Rate limit: 3 reviews per hour per IP
        $key = 'review-submit:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->with('error', 'Çok fazla yorum gönderimi yapıldı. Lütfen daha sonra tekrar deneyin.');
        }
        RateLimiter::hit($key, 3600);

        $validated = $request->validate([
            'reviewable_type' => 'required|in:App\Models\Page,App\Models\Article',
            'reviewable_id' => 'required|integer',
            'author_name' => 'required|string|max:100',
            'author_email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        // Verify reCAPTCHA if enabled
        if (config('cms.recaptcha.enabled')) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if ($recaptchaResponse) {
                try {
                    $result = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                        'secret' => config('cms.recaptcha.secret_key'),
                        'response' => $recaptchaResponse,
                    ]);

                    if (! $result->json('success', false)) {
                        return back()->with('error', 'reCAPTCHA doğrulaması başarısız.')->withInput();
                    }
                } catch (\Throwable $e) {
                    Log::error('reCAPTCHA verification failed: ' . $e->getMessage());
                }
            }
        }

        // Verify entity exists
        $entityClass = $validated['reviewable_type'];
        $entity = $entityClass::find($validated['reviewable_id']);
        if (! $entity) {
            return back()->with('error', 'İlgili içerik bulunamadı.');
        }

        Review::create([
            'reviewable_type' => $validated['reviewable_type'],
            'reviewable_id' => $validated['reviewable_id'],
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'] ?? null,
            'rating' => $validated['rating'],
            'body' => $validated['comment'],
            'is_approved' => false, // Require admin approval
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Yorumunuz başarıyla gönderildi. Onaylandıktan sonra yayınlanacaktır.');
    }
}
