<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ArticleCollection;
use App\Http\Resources\Api\ArticleResource;
use App\Models\Article;
use App\Services\SeoManager\SeoManager;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(protected SeoManager $seoManager) {}

    public function index(Request $request): ArticleCollection
    {
        $perPage = min(max((int) $request->integer('limit', 12), 1), 50);

        $articles = Article::query()
            ->published()
            ->with(['page', 'seo', 'media'])
            ->when($request->filled('page_id'), fn ($query) => $query->where('page_id', $request->integer('page_id')))
            ->when($request->boolean('featured_only'), fn ($query) => $query->featured())
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->paginate($perPage)
            ->withQueryString();

        return new ArticleCollection($articles);
    }

    public function show(string $slug)
    {
        $resolved = $this->seoManager->resolve($slug);

        abort_if(! $resolved, 404);

        if (($resolved['type'] ?? null) === 'redirect') {
            return response()->json([
                'type' => 'redirect',
                'url' => $resolved['url'],
                'status_code' => $resolved['status_code'],
            ]);
        }

        $entity = $resolved['entity'] ?? null;
        abort_if(! $entity instanceof Article, 404);
        abort_if($entity->status !== 'published', 404);

        $entity->loadMissing(['page', 'seo', 'language', 'media']);

        return ArticleResource::make($entity);
    }
}
