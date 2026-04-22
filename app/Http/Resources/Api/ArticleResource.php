<?php

namespace App\Http\Resources\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Article $article */
        $article = $this->resource;

        return [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'body' => $article->body,
                'published_at' => $article->published_at?->toAtomString(),
                'featured_image' => $article->getFirstMediaUrl('cover'),
                'gallery' => $article->getMedia('gallery')->map(fn ($media) => [
                    'url' => $media->getUrl(),
                    'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                    'name' => $media->name,
                ])->values(),
            ],
            'page' => [
                'id' => $article->page?->id,
                'title' => $article->page?->title,
                'slug' => $article->page?->slug,
            ],
            'seo' => [
                'title' => $article->seo?->meta_title ?: $article->title,
                'description' => $article->seo?->meta_description ?: ($article->excerpt ?: ''),
                'canonical' => $article->seo?->canonical_url ?: url($article->slug),
                'noindex' => (bool) ($article->seo?->is_noindex ?? false),
            ],
        ];
    }
}
