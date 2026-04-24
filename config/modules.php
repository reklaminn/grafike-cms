<?php

return [
    87   => [
        'name' => 'Yan Menu',
        'class' => \App\Services\ModuleRenderer\Modules\SideMenuModule::class,
        'view' => 'frontend.modules.side-menu',
        'configSchema' => [
            ['name' => 'menu_id', 'label' => 'Menü ID', 'type' => 'number'],
            ['name' => 'depth', 'label' => 'Derinlik', 'type' => 'number', 'default' => '3'],
        ],
    ],
    90   => [
        'name' => 'Icerik Blogu',
        'class' => \App\Services\ModuleRenderer\Modules\ContentBlockModule::class,
        'view' => 'frontend.modules.content-block',
        'configSchema' => [
            ['name' => 'limit', 'label' => 'Gösterilecek Yazı Sayısı', 'type' => 'number', 'default' => '10'],
            ['name' => 'order', 'label' => 'Sıralama', 'type' => 'select', 'options' => 'newest,oldest,sort_order', 'default' => 'sort_order'],
        ],
    ],
    110  => [
        'name' => 'Sayfa Basligi',
        'class' => \App\Services\ModuleRenderer\Modules\PageHeaderModule::class,
        'view' => 'frontend.modules.page-header',
        'configSchema' => [
            ['name' => 'show_breadcrumb', 'label' => 'Breadcrumb Göster', 'type' => 'boolean', 'default' => '1'],
            ['name' => 'tag', 'label' => 'HTML Tag', 'type' => 'select', 'options' => 'h1,h2,h3', 'default' => 'h1'],
        ],
    ],
    112  => [
        'name' => 'Harita',
        'class' => \App\Services\ModuleRenderer\Modules\MapModule::class,
        'view' => 'frontend.modules.map',
        'configSchema' => [
            ['name' => 'lat', 'label' => 'Enlem', 'type' => 'text'],
            ['name' => 'lng', 'label' => 'Boylam', 'type' => 'text'],
            ['name' => 'zoom', 'label' => 'Yakınlık', 'type' => 'number', 'default' => '15'],
            ['name' => 'height', 'label' => 'Yükseklik (px)', 'type' => 'number', 'default' => '400'],
        ],
    ],
    114  => [
        'name' => 'Resim Listeleme',
        'class' => \App\Services\ModuleRenderer\Modules\ImageListingModule::class,
        'view' => 'frontend.modules.image-listing',
        'configSchema' => [
            ['name' => 'columns', 'label' => 'Kolon Sayısı', 'type' => 'number', 'default' => '4'],
            ['name' => 'limit', 'label' => 'Gösterilecek Resim', 'type' => 'number', 'default' => '12'],
        ],
    ],
    115  => [
        'name' => 'Tam Icerik',
        'class' => \App\Services\ModuleRenderer\Modules\FullContentModule::class,
        'view' => 'frontend.modules.full-content',
        'configSchema' => [],
    ],
    121  => [
        'name' => 'Filtre Menu',
        'class' => \App\Services\ModuleRenderer\Modules\FilterMenuModule::class,
        'view' => 'frontend.modules.filter-menu',
        'configSchema' => [
            ['name' => 'style', 'label' => 'Stil', 'type' => 'select', 'options' => 'tabs,pills,buttons', 'default' => 'tabs'],
        ],
    ],
    123  => [
        'name' => 'Kategori Listeleme',
        'class' => \App\Services\ModuleRenderer\Modules\CategoryListingModule::class,
        'view' => 'frontend.modules.category-listing',
        'configSchema' => [
            ['name' => 'columns', 'label' => 'Kolon Sayısı', 'type' => 'number', 'default' => '3'],
            ['name' => 'show_image', 'label' => 'Resim Göster', 'type' => 'boolean', 'default' => '1'],
        ],
    ],
    125  => [
        'name' => 'E-Katalog',
        'class' => \App\Services\ModuleRenderer\Modules\ECatalogModule::class,
        'view' => 'frontend.modules.e-catalog',
        'configSchema' => [
            ['name' => 'limit', 'label' => 'Gösterilecek Sayı', 'type' => 'number', 'default' => '10'],
        ],
    ],
    130  => [
        'name' => 'Makale Listeleme',
        'class' => \App\Services\ModuleRenderer\Modules\ArticleListingModule::class,
        'view' => 'frontend.modules.article-listing',
        'configSchema' => [
            ['name' => 'limit', 'label' => 'Gösterilecek Yazı', 'type' => 'number', 'default' => '10'],
            ['name' => 'columns', 'label' => 'Kolon Sayısı', 'type' => 'number', 'default' => '1'],
            ['name' => 'show_image', 'label' => 'Resim Göster', 'type' => 'boolean', 'default' => '1'],
            ['name' => 'show_date', 'label' => 'Tarih Göster', 'type' => 'boolean', 'default' => '1'],
            ['name' => 'show_excerpt', 'label' => 'Özet Göster', 'type' => 'boolean', 'default' => '1'],
            ['name' => 'paginate', 'label' => 'Sayfalama', 'type' => 'boolean', 'default' => '0'],
        ],
    ],
    131  => [
        'name' => 'Kategori Kartlari',
        'class' => \App\Services\ModuleRenderer\Modules\CategoryCardsModule::class,
        'view' => 'frontend.modules.category-cards',
        'configSchema' => [
            ['name' => 'columns', 'label' => 'Kolon Sayısı', 'type' => 'number', 'default' => '3'],
            ['name' => 'style', 'label' => 'Kart Stili', 'type' => 'select', 'options' => 'card,overlay,minimal', 'default' => 'card'],
        ],
    ],
    134  => [
        'name' => 'Resim Galerisi',
        'class' => \App\Services\ModuleRenderer\Modules\ImageGalleryModule::class,
        'view' => 'frontend.modules.image-gallery',
        'configSchema' => [
            ['name' => 'columns', 'label' => 'Kolon Sayısı', 'type' => 'number', 'default' => '4'],
            ['name' => 'lightbox', 'label' => 'Lightbox', 'type' => 'boolean', 'default' => '1'],
        ],
    ],
    135  => [
        'name' => 'Icerik Kartlari',
        'class' => \App\Services\ModuleRenderer\Modules\ContentCardsModule::class,
        'view' => 'frontend.modules.content-cards',
        'configSchema' => [
            ['name' => 'columns', 'label' => 'Kolon Sayısı', 'type' => 'number', 'default' => '3'],
            ['name' => 'limit', 'label' => 'Gösterilecek Sayı', 'type' => 'number', 'default' => '6'],
            ['name' => 'show_image', 'label' => 'Resim Göster', 'type' => 'boolean', 'default' => '1'],
            ['name' => 'show_excerpt', 'label' => 'Özet Göster', 'type' => 'boolean', 'default' => '1'],
        ],
    ],
    136  => [
        'name' => 'Logo Menu',
        'class' => \App\Services\ModuleRenderer\Modules\LogoMenuModule::class,
        'view' => 'frontend.modules.logo-menu',
        'configSchema' => [],
    ],
    137  => [
        'name' => 'Ust Menu',
        'class' => \App\Services\ModuleRenderer\Modules\TopMenuModule::class,
        'view' => 'frontend.modules.top-menu',
        'configSchema' => [
            ['name' => 'menu_id', 'label' => 'Menü ID', 'type' => 'number'],
        ],
    ],
    282  => [
        'name' => '404 Sayfasi',
        'class' => \App\Services\ModuleRenderer\Modules\NotFoundModule::class,
        'view' => 'frontend.modules.not-found',
        'configSchema' => [],
    ],
    293  => [
        'name' => 'Yorumlar',
        'class' => \App\Services\ModuleRenderer\Modules\ReviewsModule::class,
        'view' => 'frontend.modules.reviews',
        'configSchema' => [
            ['name' => 'limit', 'label' => 'Gösterilecek Yorum', 'type' => 'number', 'default' => '10'],
            ['name' => 'allow_submit', 'label' => 'Yorum Gönder', 'type' => 'boolean', 'default' => '1'],
        ],
    ],
    1381 => [
        'name' => 'Kurumsal Menu',
        'class' => \App\Services\ModuleRenderer\Modules\CorporateMenuModule::class,
        'view' => 'frontend.modules.corporate-menu',
        'configSchema' => [],
    ],
    9999 => [
        'name' => 'Listeleme',
        'class' => \App\Services\ModuleRenderer\Modules\ListingModule::class,
        'view' => 'frontend.modules.listing',
        'configSchema' => [
            ['name' => 'limit', 'label' => 'Gösterilecek Sayı', 'type' => 'number', 'default' => '20'],
            ['name' => 'paginate', 'label' => 'Sayfalama', 'type' => 'boolean', 'default' => '1'],
        ],
    ],
    1501 => [
        'name' => 'Hero Banner',
        'class' => \App\Services\ModuleRenderer\Modules\HeroBannerModule::class,
        'view' => 'frontend.modules.hero-banner',
        'configSchema' => [
            ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'default' => 'Sayfanız için güçlü bir başlık'],
            ['name' => 'subtitle', 'label' => 'Alt Başlık', 'type' => 'textarea', 'default' => 'Kısa bir açıklama veya değer önermesi ekleyin.'],
            ['name' => 'button_text', 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'İncele'],
            ['name' => 'button_url', 'label' => 'Buton Linki', 'type' => 'text', 'default' => '#'],
            ['name' => 'background_image', 'label' => 'Arkaplan Görsel URL', 'type' => 'text'],
            ['name' => 'align', 'label' => 'Hizalama', 'type' => 'select', 'options' => 'left,center,right', 'default' => 'left'],
            ['name' => 'theme', 'label' => 'Tema', 'type' => 'select', 'options' => 'dark,light,brand', 'default' => 'dark'],
        ],
    ],
    1502 => [
        'name' => 'Metin Blogu',
        'class' => \App\Services\ModuleRenderer\Modules\TextBlockModule::class,
        'view' => 'frontend.modules.text-block',
        'configSchema' => [
            ['name' => 'title', 'label' => 'Başlık', 'type' => 'text'],
            ['name' => 'body', 'label' => 'Metin / HTML', 'type' => 'textarea'],
            ['name' => 'align', 'label' => 'Hizalama', 'type' => 'select', 'options' => 'left,center,right', 'default' => 'left'],
            ['name' => 'max_width', 'label' => 'Maksimum Genişlik', 'type' => 'select', 'options' => 'full,4xl,3xl,2xl', 'default' => '3xl'],
        ],
    ],
    1503 => [
        'name' => 'CTA Banner',
        'class' => \App\Services\ModuleRenderer\Modules\CtaBannerModule::class,
        'view' => 'frontend.modules.cta-banner',
        'configSchema' => [
            ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'default' => 'Güçlü bir çağrı alanı'],
            ['name' => 'body', 'label' => 'Açıklama', 'type' => 'textarea', 'default' => 'Ziyaretçiyi aksiyona yönlendirecek kısa bir açıklama yazın.'],
            ['name' => 'button_text', 'label' => 'Buton Metni', 'type' => 'text', 'default' => 'Hemen Başla'],
            ['name' => 'button_url', 'label' => 'Buton Linki', 'type' => 'text', 'default' => '#'],
            ['name' => 'secondary_text', 'label' => 'İkincil Link Metni', 'type' => 'text'],
            ['name' => 'secondary_url', 'label' => 'İkincil Link', 'type' => 'text'],
            ['name' => 'theme', 'label' => 'Tema', 'type' => 'select', 'options' => 'brand,dark,light', 'default' => 'brand'],
        ],
    ],
    1504 => [
        'name' => 'Spacer',
        'class' => \App\Services\ModuleRenderer\Modules\SpacerModule::class,
        'view' => 'frontend.modules.spacer',
        'configSchema' => [
            ['name' => 'height', 'label' => 'Yükseklik (px)', 'type' => 'number', 'default' => '64'],
            ['name' => 'height_mobile', 'label' => 'Mobil Yükseklik (px)', 'type' => 'number', 'default' => '32'],
        ],
    ],
    1505 => [
        'name' => 'Video Embed',
        'class' => \App\Services\ModuleRenderer\Modules\VideoEmbedModule::class,
        'view' => 'frontend.modules.video-embed',
        'configSchema' => [
            ['name' => 'title', 'label' => 'Başlık', 'type' => 'text'],
            ['name' => 'embed_url', 'label' => 'YouTube/Vimeo/Gömme URL', 'type' => 'text'],
            ['name' => 'aspect_ratio', 'label' => 'Oran', 'type' => 'select', 'options' => '16:9,4:3,1:1', 'default' => '16:9'],
        ],
    ],
    1506 => [
        'name' => 'Custom HTML',
        'class' => \App\Services\ModuleRenderer\Modules\TextBlockModule::class,
        'view' => 'frontend.modules.text-block',
        'configSchema' => [
            ['name' => 'body', 'label' => 'HTML İçerik', 'type' => 'textarea', 'default' => '<div class="custom-html-block">Özel HTML içeriğinizi buraya ekleyin.</div>'],
        ],
    ],
];
