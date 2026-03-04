<?php

namespace App\Enums;

enum ContentType: int
{
    case Standard = 0;
    case Detail = 1;
    case Gallery = 3;
    case News = 4;
    case Video = 5;
    case ContactForm = 7;
    case Tour = 8;
    case Product = 9;
    case ECatalog = 10;
    case Map = 11;
    case Blog = 12;

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standart',
            self::Detail => 'Detay',
            self::Gallery => 'Galeri',
            self::News => 'Haber',
            self::Video => 'Video',
            self::ContactForm => 'İletişim Formu',
            self::Tour => 'Tur',
            self::Product => 'Ürün',
            self::ECatalog => 'E-Katalog',
            self::Map => 'Harita',
            self::Blog => 'Blog',
        };
    }
}
