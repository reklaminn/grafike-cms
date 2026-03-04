<?php

namespace App\Console\Commands\Legacy;

use App\Models\Article;
use App\Enums\ContentType;

class MigrateArticlesCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:articles
                            {--fresh : Truncate articles table before migrating}
                            {--page= : Only migrate articles for a specific legacy page ID}';

    protected $description = 'Migrate articles from legacy yazilar table';

    public function handle(): int
    {
        $this->info('📝 Starting Articles Migration (yazilar → articles)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing articles. Continue?')) {
            Article::truncate();
            $this->warn('Articles table truncated.');
        }

        $query = $this->legacy('yazilar');

        if ($pageId = $this->option('page')) {
            $query->where('kategori', $pageId);
            $this->info("  Filtering by legacy page ID: {$pageId}");
        }

        $legacyArticles = $query->orderBy('id')->get();

        if ($legacyArticles->isEmpty()) {
            $this->warn('No articles found in legacy database.');
            return self::SUCCESS;
        }

        $this->info("  Found {$legacyArticles->count()} articles to migrate.");

        $bar = $this->output->createProgressBar($legacyArticles->count());
        $bar->start();

        foreach ($legacyArticles as $legacyArticle) {
            try {
                $title = $this->toUtf8($legacyArticle->yazib ?? 'Untitled');
                $body = $this->cleanHtml($legacyArticle->bilgi1 ?? null);
                $excerpt = $this->toUtf8($legacyArticle->yazi ?? null);
                $extraInfo = $this->toUtf8($legacyArticle->bilgi3 ?? null);

                // Map legacy page_id
                $pageId = null;
                if (!empty($legacyArticle->kategori)) {
                    $pageId = $this->mapLegacyId((int) $legacyArticle->kategori, 'page');
                }

                // Map language
                $languageId = $this->mapLegacyId((int) ($legacyArticle->dil ?? 240), 'language');

                // Map form
                $formId = null;
                if (!empty($legacyArticle->form) && $legacyArticle->form != '0') {
                    $formId = $this->mapLegacyId((int) $legacyArticle->form, 'form');
                }

                // Determine content type
                $contentTypeId = $this->resolveContentType($legacyArticle);

                $slug = $this->generateSlug($title, 'articles', 'slug');

                $article = Article::create([
                    'title' => $title,
                    'body' => $body,
                    'excerpt' => $excerpt,
                    'page_id' => $pageId,
                    'language_id' => $languageId ?? 1,
                    'status' => ($legacyArticle->durum ?? '1') == '1' ? 'published' : 'draft',
                    'sort_order' => (int) ($legacyArticle->sira ?? 0),
                    'slug' => $slug,
                    'external_url' => $this->toUtf8($legacyArticle->link ?? null),
                    'content_type_id' => $contentTypeId,
                    'form_id' => $formId,
                    'is_featured' => (bool) ($legacyArticle->ilksayfa ?? 0),
                    'extra_info' => $extraInfo,
                    'published_at' => $this->parseDate($legacyArticle->tarih ?? null),
                    'display_date' => $this->parseDate($legacyArticle->tarih ?? null),
                    'legacy_id' => $legacyArticle->id,
                ]);

                // Handle language variants (sira1 field used for language grouping)
                if (!empty($legacyArticle->sira1) && $legacyArticle->sira1 != '0') {
                    // sira1 links articles as translations of each other
                    $parentLegacyId = (int) $legacyArticle->sira1;
                    $parentArticleId = $this->mapLegacyId($parentLegacyId, 'article');
                    if ($parentArticleId) {
                        $article->update(['parent_article_id' => $parentArticleId]);
                    }
                }

                $this->storeLegacyMapping($legacyArticle->id, $article->id, 'article');
                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate article ID {$legacyArticle->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->printSummary('Articles');

        return self::SUCCESS;
    }

    /**
     * Resolve the content type from legacy module flags.
     */
    protected function resolveContentType(object $legacyArticle): int
    {
        // Check if the parent page has a specific module type
        if (!empty($legacyArticle->urunid) && $legacyArticle->urunid != '0') {
            return ContentType::Product->value;
        }

        if (($legacyArticle->modulmu ?? '0') == '1') {
            return ContentType::Standard->value;
        }

        return ContentType::Standard->value;
    }
}
