<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Admin;
use App\Models\Article;
use App\Models\Form;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['page', 'language', 'author']);

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($pageId = $request->input('page_id')) {
            $query->where('page_id', $pageId);
        }

        if ($langId = $request->input('language_id')) {
            $query->where('language_id', $langId);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', (bool) $request->input('is_featured'));
        }

        $articles  = $query->latest('updated_at')->paginate(25);
        $languages = Language::where('is_active', true)->get();
        $pages     = Page::orderBy('title')->get(['id', 'title']);

        return view('admin.articles.index', compact('articles', 'languages', 'pages'));
    }

    public function create(Request $request)
    {
        $languages      = Language::where('is_active', true)->get();
        $pages          = Page::orderBy('title')->get(['id', 'title']);
        $forms          = Form::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $admins         = Admin::orderBy('name')->get(['id', 'name']);
        $selectedPageId = $request->input('page_id');

        return view('admin.articles.create', compact('languages', 'pages', 'forms', 'admins', 'selectedPageId'));
    }

    public function store(ArticleRequest $request)
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['author_id'] = auth('admin')->id();

        if (empty($data['published_at']) && $data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $article = Article::create($data);

        if ($request->hasFile('cover_image')) {
            $article->addMediaFromRequest('cover_image')
                ->toMediaCollection('cover');
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $article->addMedia($image)->toMediaCollection('gallery');
            }
        }

        // Handle SEO
        $this->saveSeo($article, $request);

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('success', 'Yazı başarıyla oluşturuldu.');
    }

    public function edit(Article $article)
    {
        $article->load(['page', 'language', 'seo', 'media', 'form']);

        $languages = Language::where('is_active', true)->get();
        $pages     = Page::orderBy('title')->get(['id', 'title']);
        $forms     = Form::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $admins    = Admin::orderBy('name')->get(['id', 'name']);

        return view('admin.articles.edit', compact('article', 'languages', 'pages', 'forms', 'admins'));
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $article->id);
        }

        $data['is_featured'] = $request->boolean('is_featured');

        $article->update($data);

        if ($request->hasFile('cover_image')) {
            $article->clearMediaCollection('cover');
            $article->addMediaFromRequest('cover_image')
                ->toMediaCollection('cover');
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $article->addMedia($image)->toMediaCollection('gallery');
            }
        }

        $this->saveSeo($article, $request);

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('success', 'Yazı başarıyla güncellendi.');
    }

    public function destroyCover(Article $article)
    {
        $article->clearMediaCollection('cover');

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('success', 'Kapak görseli kaldırıldı.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Yazı başarıyla silindi.');
    }

    protected function saveSeo(Article $article, Request $request): void
    {
        if ($request->filled('seo_title') || $request->filled('seo_description')) {
            $article->seo()->updateOrCreate(
                ['seoable_id' => $article->id, 'seoable_type' => Article::class],
                [
                    'slug' => $article->slug,
                    'language_id' => $article->language_id,
                    'meta_title' => $request->input('seo_title'),
                    'meta_description' => $request->input('seo_description'),
                    'meta_keywords' => $request->input('seo_keywords'),
                ]
            );
        }
    }

    protected function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $turkishMap = [
            'ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g',
            'ı' => 'i', 'İ' => 'i', 'ö' => 'o', 'Ö' => 'o',
            'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u',
        ];

        $slug = Str::slug(strtr($title, $turkishMap));
        $original = $slug;
        $counter = 1;

        $query = Article::where('slug', $slug);
        if ($excludeId) $query->where('id', '!=', $excludeId);

        while ($query->exists()) {
            $slug = $original . '-' . $counter++;
            $query = Article::where('slug', $slug);
            if ($excludeId) $query->where('id', '!=', $excludeId);
        }

        return $slug;
    }
}
