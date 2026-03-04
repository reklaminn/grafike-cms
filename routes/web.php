<?php

use App\Http\Controllers\Frontend\FormSubmissionController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\MemberAuthController;
use App\Http\Controllers\Frontend\PageUnlockController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\SitemapController;
use Illuminate\Support\Facades\Route;

// Sitemap
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// robots.txt (dynamic)
Route::get('robots.txt', function () {
    $content = "User-agent: *\nAllow: /\n";
    $content .= 'Sitemap: ' . url('sitemap.xml') . "\n";

    return response($content, 200)->header('Content-Type', 'text/plain');
});

// Language switch
Route::get('lang/{code}', function (string $code) {
    if (in_array($code, ['tr', 'en', 'de', 'ru', 'fr', 'ar'])) {
        session(['locale' => $code]);
    }

    return redirect()->back();
})->name('lang.switch');

// Form submission
Route::post('forms/{form}/submit', [FormSubmissionController::class, 'store'])
    ->name('forms.submit');

// Review submission
Route::post('reviews', [ReviewController::class, 'store'])
    ->name('reviews.store');

// Page unlock (password-protected pages)
Route::post('pages/{page}/unlock', [PageUnlockController::class, 'unlock'])
    ->name('pages.unlock');

// Member Authentication
Route::prefix('member')->name('member.')->group(function () {
    Route::get('login', [MemberAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [MemberAuthController::class, 'login'])->name('login.submit');
    Route::get('register', [MemberAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [MemberAuthController::class, 'register'])->name('register.submit');

    Route::middleware('member.auth')->group(function () {
        Route::get('profile', [MemberAuthController::class, 'profile'])->name('profile');
        Route::put('profile', [MemberAuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('logout', [MemberAuthController::class, 'logout'])->name('logout');
    });
});

// Frontend catch-all routes (must be last!)
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('{slug}', [FrontendController::class, 'show'])
    ->where('slug', '^(?!admin|member).*$')
    ->name('page.show');
