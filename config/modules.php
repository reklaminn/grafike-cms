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
];
