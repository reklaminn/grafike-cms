<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Article;
use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageTemplate;
use App\Models\SeoEntry;
use App\Models\SectionTemplate;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\SiteTemplate;
use App\Models\Theme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FrontendTemplateDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::withTrashed()->updateOrCreate(
            ['email' => 'admin@grafike-next.local'],
            [
                'username' => 'admin',
                'name' => 'Next CMS Admin',
                'password' => 'admin123',
                'deleted_at' => null,
            ]
        );

        $language = Language::updateOrCreate(
            ['code' => 'tr'],
            [
                'name' => 'Türkçe',
                'locale' => 'tr_TR',
                'is_active' => true,
                'direction' => 'ltr',
                'sort_order' => 1,
            ]
        );

        $theme = Theme::updateOrCreate(
            ['slug' => 'porto-furniture'],
            [
                'name' => 'Porto Furniture',
                'engine' => 'nextjs-basic-html',
                'description' => 'Basic HTML Section Mode demo theme pack.',
                'assets_json' => [
                    'css' => [
                        '/themes/porto-furniture/vendor.css',
                        '/themes/porto-furniture/theme.css',
                    ],
                    'js' => [
                        '/themes/porto-furniture/theme.js',
                    ],
                ],
                'tokens_json' => [
                    'color_primary' => '#7c5a3a',
                    'color_secondary' => '#f3ede6',
                    'color_accent' => '#111827',
                    'radius_card' => '20px',
                    'radius_button' => '999px',
                    'container_width' => '1320px',
                ],
                'settings_schema_json' => [
                    'header_variant' => ['type' => 'select'],
                    'footer_variant' => ['type' => 'select'],
                ],
                'is_active' => true,
            ]
        );

        $heroSectionTemplate = SectionTemplate::updateOrCreate(
            ['theme_id' => $theme->id, 'type' => 'hero', 'variation' => 'porto-split'],
            [
                'name' => 'Hero / Porto Split',
                'render_mode' => 'html',
                'html_template' => <<<'HTML'
<section class="hero hero--porto-split">
  <div class="container">
    <p class="eyebrow">{{eyebrow}}</p>
    <h1>{{title}}</h1>
    <p class="subtitle">{{subtitle}}</p>
    <a class="button" href="{{button_url}}">{{button_text}}</a>
  </div>
</section>
HTML,
                'schema_json' => [
                    'eyebrow' => ['type' => 'text'],
                    'title' => ['type' => 'text'],
                    'subtitle' => ['type' => 'textarea'],
                    'button_text' => ['type' => 'text'],
                    'button_url' => ['type' => 'text'],
                ],
                'default_content_json' => [
                    'eyebrow' => 'Porto Furniture',
                    'title' => 'Modern Furniture Collections',
                    'subtitle' => 'Curated living spaces for premium brands.',
                    'button_text' => 'Koleksiyonu Incele',
                    'button_url' => '/koleksiyon',
                ],
                'is_active' => true,
            ]
        );

        $featureSectionTemplate = SectionTemplate::updateOrCreate(
            ['theme_id' => $theme->id, 'type' => 'features', 'variation' => 'porto-icons'],
            [
                'name' => 'Features / Porto Icons',
                'render_mode' => 'html',
                'html_template' => <<<'HTML'
<section class="features features--porto-icons">
  <div class="container">
    <h2>{{title}}</h2>
    <p>{{description}}</p>
  </div>
</section>
HTML,
                'schema_json' => [
                    'title' => ['type' => 'text'],
                    'description' => ['type' => 'textarea'],
                ],
                'default_content_json' => [
                    'title' => 'Neden Biz?',
                    'description' => 'Reusable HTML sections ile hızlı kurumsal site üretimi.',
                ],
                'is_active' => true,
            ]
        );

        $pageTemplate = PageTemplate::updateOrCreate(
            ['slug' => 'porto-furniture-home'],
            [
                'theme_id' => $theme->id,
                'name' => 'Porto Furniture Home',
                'page_type' => 'home',
                'sections_json' => [
                    [
                        'id' => 'hero_default',
                        'type' => 'hero',
                        'variation' => 'porto-split',
                        'render_mode' => 'html',
                        'section_template_id' => $heroSectionTemplate->id,
                        'is_active' => true,
                        'sort_order' => 1,
                        'content' => $heroSectionTemplate->default_content_json,
                    ],
                    [
                        'id' => 'features_default',
                        'type' => 'features',
                        'variation' => 'porto-icons',
                        'render_mode' => 'html',
                        'section_template_id' => $featureSectionTemplate->id,
                        'is_active' => true,
                        'sort_order' => 2,
                        'content' => $featureSectionTemplate->default_content_json,
                    ],
                ],
                'default_settings_json' => [
                    'header_variant' => 'porto-furniture-header',
                    'footer_variant' => 'porto-furniture-footer',
                ],
                'is_active' => true,
            ]
        );

        $siteTemplate = SiteTemplate::updateOrCreate(
            ['slug' => 'porto-furniture-demo'],
            [
                'theme_id' => $theme->id,
                'name' => 'Porto Furniture Demo Site',
                'description' => 'Basic HTML Section Mode reusable site template.',
                'snapshot_json' => [
                    'page_template_slug' => $pageTemplate->slug,
                    'theme_slug' => $theme->slug,
                ],
                'is_active' => true,
            ]
        );

        $site = Site::updateOrCreate(
            ['slug' => 'grafike-demo'],
            [
                'name' => 'Grafike Next Demo',
                'domain' => 'demo.grafike.test',
                'theme_id' => $theme->id,
                'site_template_id' => $siteTemplate->id,
                'tokens_json' => [
                    'color_primary' => '#8b5e3c',
                    'color_secondary' => '#f7efe7',
                    'radius_card' => '24px',
                ],
                'settings_json' => [
                    'header_variant' => 'porto-furniture-header',
                    'footer_variant' => 'porto-furniture-footer',
                ],
                'custom_css' => '.hero--porto-split { padding: 6rem 0; }',
                'status' => 'active',
                'is_primary' => true,
            ]
        );

        $settings = [
            ['key' => 'site.title', 'value' => $site->name, 'group' => 'general'],
            ['key' => 'site.footer_text', 'value' => '© 2026 Grafike Next Demo', 'group' => 'general'],
            ['key' => 'contact.phone', 'value' => '+90 212 555 0000', 'group' => 'contact'],
            ['key' => 'contact.email', 'value' => 'hello@grafike.test', 'group' => 'contact'],
            ['key' => 'contact.address', 'value' => 'Istanbul, Turkiye', 'group' => 'contact'],
            ['key' => 'social.instagram', 'value' => 'https://instagram.com/grafike', 'group' => 'social'],
            ['key' => 'design.color_primary', 'value' => '#8b5e3c', 'group' => 'design'],
            ['key' => 'design.color_secondary', 'value' => '#f7efe7', 'group' => 'design'],
            ['key' => 'design.color_accent', 'value' => '#111827', 'group' => 'design'],
            ['key' => 'theme.header_variant', 'value' => 'porto-furniture-header', 'group' => 'theme'],
            ['key' => 'theme.footer_variant', 'value' => 'porto-furniture-footer', 'group' => 'theme'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['site_id' => $site->id, 'key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group'], 'type' => 'text']
            );
        }

        $homepage = Page::updateOrCreate(
            ['site_id' => $site->id, 'slug' => 'home', 'language_id' => $language->id],
            [
                'title' => 'Ana Sayfa',
                'status' => 'published',
                'show_in_menu' => true,
                'sort_order' => 1,
                'template' => 'porto-furniture-home',
                'page_template_id' => $pageTemplate->id,
                'frontend_variant' => 'porto-furniture-home',
                'sections_json' => [
                    [
                        'id' => 'hero_home',
                        'type' => 'hero',
                        'variation' => 'porto-split',
                        'render_mode' => 'html',
                        'section_template_id' => $heroSectionTemplate->id,
                        'is_active' => true,
                        'sort_order' => 1,
                        'content' => [
                            'eyebrow' => 'Grafike Demo',
                            'title' => 'Furniture layoutlarini reusable template olarak yonetin',
                            'subtitle' => 'Porto benzeri section akisini Next.js frontend ve Laravel backend ile yonetin.',
                            'button_text' => 'Demo Blog',
                            'button_url' => '/blog',
                        ],
                    ],
                    [
                        'id' => 'features_home',
                        'type' => 'features',
                        'variation' => 'porto-icons',
                        'render_mode' => 'html',
                        'section_template_id' => $featureSectionTemplate->id,
                        'is_active' => true,
                        'sort_order' => 2,
                        'content' => [
                            'title' => 'Basic HTML Section Mode',
                            'description' => 'Hazir HTML theme sectionlarini CMS verisiyle siralayip farkli firmalarda tekrar kullanabilirsiniz.',
                        ],
                    ],
                ],
                'layout_json' => [],
                'show_breadcrumb' => false,
                'view_count' => 0,
            ]
        );

        SeoEntry::updateOrCreate(
            ['seoable_id' => $homepage->id, 'seoable_type' => Page::class, 'language_id' => $language->id],
            [
                'slug' => 'home',
                'meta_title' => 'Grafike Next Demo',
                'meta_description' => 'Next.js frontend + Laravel backend demo anasayfa',
            ]
        );

        SiteSetting::set('cms.homepage_id', (string) $homepage->id, 'general', $site->id);

        $blogPage = Page::updateOrCreate(
            ['site_id' => $site->id, 'slug' => 'blog', 'language_id' => $language->id],
            [
                'title' => 'Blog',
                'status' => 'published',
                'show_in_menu' => true,
                'sort_order' => 2,
                'template' => 'blog-index',
                'frontend_variant' => 'cards_3col',
                'sections_json' => [],
                'layout_json' => [],
                'show_breadcrumb' => true,
                'view_count' => 0,
            ]
        );

        SeoEntry::updateOrCreate(
            ['seoable_id' => $blogPage->id, 'seoable_type' => Page::class, 'language_id' => $language->id],
            [
                'slug' => 'blog',
                'meta_title' => 'Blog - Grafike Next Demo',
                'meta_description' => 'Demo blog listesi',
            ]
        );

        foreach ([
            [
                'title' => 'Reusable theme pack mantigi',
                'excerpt' => 'Ayni template farkli marka tokenlari ile tekrar kullanilabilir.',
            ],
            [
                'title' => 'HTML sectionlardan Next.js templatee gecis',
                'excerpt' => 'Basic mode ile baslayip en cok kullanilan sectionlari componente cevirebilirsiniz.',
            ],
        ] as $index => $post) {
            $article = Article::updateOrCreate(
                ['site_id' => $site->id, 'slug' => Str::slug($post['title']), 'language_id' => $language->id],
                [
                    'title' => $post['title'],
                    'body' => "<p>{$post['excerpt']}</p>",
                    'excerpt' => $post['excerpt'],
                    'page_id' => $blogPage->id,
                    'status' => 'published',
                    'sort_order' => $index + 1,
                    'template' => 'blog-detail',
                    'listing_variant' => 'cards_3col',
                    'detail_variant' => 'classic',
                    'published_at' => now()->subDays($index),
                    'author_id' => $admin->id,
                ]
            );

            SeoEntry::updateOrCreate(
                ['seoable_id' => $article->id, 'seoable_type' => Article::class, 'language_id' => $language->id],
                [
                    'slug' => $article->slug,
                    'meta_title' => $article->title,
                    'meta_description' => $article->excerpt,
                ]
            );
        }

        $menu = Menu::updateOrCreate(
            ['site_id' => $site->id, 'slug' => 'header-tr'],
            [
                'name' => 'Header Menu',
                'location' => 'header',
                'theme_variant' => 'porto-furniture-header',
                'language_id' => $language->id,
                'is_active' => true,
            ]
        );

        MenuItem::updateOrCreate(
            ['menu_id' => $menu->id, 'title' => 'Ana Sayfa'],
            [
                'page_id' => $homepage->id,
                'url' => '/home',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        MenuItem::updateOrCreate(
            ['menu_id' => $menu->id, 'title' => 'Blog'],
            [
                'page_id' => $blogPage->id,
                'url' => '/blog',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        if ($this->command) {
            $this->command->info('✓ Frontend template demo verisi oluşturuldu');
            $this->command->info("✓ Site: {$site->name} ({$site->domain})");
            $this->command->info("✓ Homepage ID: {$homepage->id}");
        }
    }
}
