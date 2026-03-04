<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Article;
use App\Models\Language;
use App\Models\Menu;
use App\Models\Page;
use App\Models\SeoEntry;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin User ──────────────────────────────
        $admin = Admin::create([
            'username' => 'admin',
            'name' => 'Site Yöneticisi',
            'email' => 'admin@iraspa.com',
            'password' => 'admin123',
        ]);
        $this->command->info('✓ Admin oluşturuldu: admin / admin123');

        // ─── Languages ───────────────────────────────
        $tr = Language::create([
            'name' => 'Türkçe',
            'code' => 'tr',
            'locale' => 'tr_TR',
            'is_active' => true,
            'direction' => 'ltr',
            'sort_order' => 1,
        ]);

        $en = Language::create([
            'name' => 'English',
            'code' => 'en',
            'locale' => 'en_US',
            'is_active' => true,
            'direction' => 'ltr',
            'sort_order' => 2,
        ]);
        $this->command->info('✓ Diller oluşturuldu: Türkçe, English');

        // ─── Site Settings ───────────────────────────
        $settings = [
            ['key' => 'site.title', 'value' => 'IRASPA Demo', 'group' => 'general'],
            ['key' => 'site.company_name', 'value' => 'IRASPA Ltd.', 'group' => 'general'],
            ['key' => 'site.footer_text', 'value' => '© 2026 IRASPA. Tüm hakları saklıdır.', 'group' => 'general'],
            ['key' => 'contact.phone', 'value' => '+90 212 555 0000', 'group' => 'contact'],
            ['key' => 'contact.email', 'value' => 'info@iraspa.com', 'group' => 'contact'],
            ['key' => 'contact.address', 'value' => 'İstanbul, Türkiye', 'group' => 'contact'],
        ];

        foreach ($settings as $s) {
            SiteSetting::create(array_merge($s, ['type' => 'text']));
        }
        $this->command->info('✓ Site ayarları oluşturuldu');

        // ─── Homepage ────────────────────────────────
        $homepage = Page::create([
            'title' => 'Ana Sayfa',
            'slug' => 'ana-sayfa',
            'language_id' => $tr->id,
            'status' => 'published',
            'show_in_menu' => true,
            'sort_order' => 1,
            'module_type' => 0,
            'show_breadcrumb' => false,
            'view_count' => 0,
            'layout_json' => [
                [
                    'type' => 'body',
                    'cont' => 'container',
                    'elcss' => '',
                    'children' => [
                        [
                            [
                                'coltype' => 'col-12',
                                'children' => [
                                    [
                                        [
                                            'modulid' => 90,
                                            'json' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        SeoEntry::create([
            'seoable_id' => $homepage->id,
            'seoable_type' => Page::class,
            'slug' => 'ana-sayfa',
            'language_id' => $tr->id,
            'meta_title' => 'IRASPA Demo - Ana Sayfa',
            'meta_description' => 'IRASPA CMS demo sitesi ana sayfası',
        ]);

        // Update config homepage_id
        SiteSetting::set('cms.homepage_id', $homepage->id);

        $this->command->info("✓ Ana sayfa oluşturuldu (ID: {$homepage->id})");

        // ─── About Page ──────────────────────────────
        $about = Page::create([
            'title' => 'Hakkımızda',
            'slug' => 'hakkimizda',
            'language_id' => $tr->id,
            'status' => 'published',
            'show_in_menu' => true,
            'sort_order' => 2,
            'module_type' => 0,
            'show_breadcrumb' => true,
            'view_count' => 0,
            'layout_json' => [
                [
                    'type' => 'body',
                    'cont' => 'container',
                    'elcss' => 'py-8',
                    'children' => [
                        [
                            [
                                'coltype' => 'col-12',
                                'children' => [
                                    [
                                        [
                                            'modulid' => 90,
                                            'json' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        SeoEntry::create([
            'seoable_id' => $about->id,
            'seoable_type' => Page::class,
            'slug' => 'hakkimizda',
            'language_id' => $tr->id,
            'meta_title' => 'Hakkımızda - IRASPA Demo',
            'meta_description' => 'IRASPA hakkında bilgiler',
        ]);
        $this->command->info('✓ Hakkımızda sayfası oluşturuldu');

        // ─── Blog Page ───────────────────────────────
        $blog = Page::create([
            'title' => 'Blog',
            'slug' => 'blog',
            'language_id' => $tr->id,
            'status' => 'published',
            'show_in_menu' => true,
            'sort_order' => 3,
            'module_type' => 0,
            'show_breadcrumb' => true,
            'view_count' => 0,
            'layout_json' => [
                [
                    'type' => 'body',
                    'cont' => 'container',
                    'elcss' => 'py-8',
                    'children' => [
                        [
                            [
                                'coltype' => 'col-12',
                                'children' => [
                                    [
                                        [
                                            'modulid' => 135,
                                            'json' => [
                                                ['name' => 'limit', 'value' => '6'],
                                                ['name' => 'display', 'value' => 'grid'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        SeoEntry::create([
            'seoable_id' => $blog->id,
            'seoable_type' => Page::class,
            'slug' => 'blog',
            'language_id' => $tr->id,
            'meta_title' => 'Blog - IRASPA Demo',
            'meta_description' => 'IRASPA Demo blog yazıları',
        ]);

        // Blog Articles
        $blogPosts = [
            ['title' => 'Laravel 12 ile Modern CMS Geliştirme', 'excerpt' => 'Laravel 12 ve modern araçlarla nasıl güçlü bir CMS geliştirilir.'],
            ['title' => 'Classic ASP\'den Laravel\'e Geçiş Rehberi', 'excerpt' => 'Eski ASP uygulamalarınızı Laravel\'e taşımanın en iyi yolları.'],
            ['title' => 'PHP 8.5 Yenilikleri', 'excerpt' => 'PHP 8.5 ile gelen yeni özellikler ve performans iyileştirmeleri.'],
        ];

        foreach ($blogPosts as $i => $post) {
            $article = Article::create([
                'title' => $post['title'],
                'body' => "<p>{$post['excerpt']}</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>",
                'excerpt' => $post['excerpt'],
                'page_id' => $blog->id,
                'language_id' => $tr->id,
                'status' => 'published',
                'sort_order' => $i + 1,
                'slug' => \Str::slug($post['title']),
                'is_featured' => $i === 0,
                'meta_description' => $post['excerpt'],
                'published_at' => now()->subDays($i * 3),
                'author_id' => $admin->id,
            ]);

            SeoEntry::create([
                'seoable_id' => $article->id,
                'seoable_type' => Article::class,
                'slug' => \Str::slug($post['title']),
                'language_id' => $tr->id,
                'meta_title' => $post['title'],
                'meta_description' => $post['excerpt'],
            ]);
        }
        $this->command->info('✓ Blog sayfası ve 3 yazı oluşturuldu');

        // ─── Contact Page ────────────────────────────
        $contact = Page::create([
            'title' => 'İletişim',
            'slug' => 'iletisim',
            'language_id' => $tr->id,
            'status' => 'published',
            'show_in_menu' => true,
            'sort_order' => 4,
            'module_type' => 0,
            'show_breadcrumb' => true,
            'view_count' => 0,
            'layout_json' => [
                [
                    'type' => 'body',
                    'cont' => 'container',
                    'elcss' => 'py-8',
                    'children' => [
                        [
                            [
                                'coltype' => 'col-12',
                                'children' => [
                                    [
                                        [
                                            'modulid' => 90,
                                            'json' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        SeoEntry::create([
            'seoable_id' => $contact->id,
            'seoable_type' => Page::class,
            'slug' => 'iletisim',
            'language_id' => $tr->id,
            'meta_title' => 'İletişim - IRASPA Demo',
            'meta_description' => 'Bizimle iletişime geçin',
        ]);
        $this->command->info('✓ İletişim sayfası oluşturuldu');

        // ─── Main Menu ───────────────────────────────
        $menu = Menu::create([
            'name' => 'Ana Menü',
            'slug' => 'ana-menu',
            'location' => 'header',
            'language_id' => $tr->id,
        ]);

        $menuItems = [
            ['title' => 'Ana Sayfa', 'url' => '/', 'target' => '_self', 'sort_order' => 1],
            ['title' => 'Hakkımızda', 'url' => '/hakkimizda', 'target' => '_self', 'sort_order' => 2],
            ['title' => 'Blog', 'url' => '/blog', 'target' => '_self', 'sort_order' => 3],
            ['title' => 'İletişim', 'url' => '/iletisim', 'target' => '_self', 'sort_order' => 4],
        ];

        foreach ($menuItems as $item) {
            $menu->items()->create($item);
        }
        $this->command->info('✓ Ana menü oluşturuldu');

        // ─── Summary ────────────────────────────────
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  Demo veriler başarıyla oluşturuldu!');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->newLine();
        $this->command->info('  Admin Panel: /admin');
        $this->command->info('  Kullanıcı:   admin');
        $this->command->info('  Şifre:       admin123');
        $this->command->newLine();
        $this->command->info('  Sayfalar: Ana Sayfa, Hakkımızda, Blog, İletişim');
        $this->command->info('  Blog yazıları: 3 adet');
        $this->command->info('  Diller: Türkçe, English');
        $this->command->newLine();
    }
}
