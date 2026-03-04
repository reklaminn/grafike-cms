<?php

namespace App\Services\ModuleRenderer\Modules;

use App\Models\Article;
use App\Models\Page;
use App\Models\Review;

class ReviewsModule extends BaseModule
{
    public function getName(): string
    {
        return 'Yorumlar';
    }

    protected function getViewName(): string
    {
        return 'frontend.modules.reviews';
    }

    protected function getData(array $config, Page $page, ?Article $article): array
    {
        $entityType = $article ? 'article' : 'page';
        $entityId = $article?->id ?? $page->id;
        $perPage = $config['ladet'] ?? 10;

        $reviews = Review::where('reviewable_type', $entityType === 'article' ? Article::class : Page::class)
            ->where('reviewable_id', $entityId)
            ->where('is_approved', true)
            ->latest()
            ->paginate($perPage);

        $averageRating = Review::where('reviewable_type', $entityType === 'article' ? Article::class : Page::class)
            ->where('reviewable_id', $entityId)
            ->where('is_approved', true)
            ->avg('rating') ?? 0;

        $totalReviews = Review::where('reviewable_type', $entityType === 'article' ? Article::class : Page::class)
            ->where('reviewable_id', $entityId)
            ->where('is_approved', true)
            ->count();

        return [
            'reviews' => $reviews,
            'averageRating' => round($averageRating, 1),
            'totalReviews' => $totalReviews,
            'entityType' => $entityType,
            'entityId' => $entityId,
            'title' => $config['baslik'] ?? 'Yorumlar',
            'showForm' => $config['showForm'] ?? true,
            'recaptchaKey' => config('cms.recaptcha.site_key', ''),
            'template' => $config['temp'] ?? null,
        ];
    }
}
