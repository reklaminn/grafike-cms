<?php

namespace App\Services\TemplateEngine;

use App\Models\Article;
use App\Models\Page;

class TemplateEngine
{
    protected array $replacements = [];

    public function render(string $template, Page $page, ?Article $article = null): string
    {
        $this->buildReplacements($page, $article);

        $result = $template;
        foreach ($this->replacements as $token => $value) {
            $result = str_replace("#{$token}#", $value ?? '', $result);
        }

        // Handle dynamic function tokens like #ozellikcek(...)#
        $result = preg_replace_callback('/#ozellikcek\(([^)]+)\)#/', function ($matches) {
            return $this->resolveCustomField($matches[1]);
        }, $result);

        // Handle calculation tokens like #hesapla(...)#
        $result = preg_replace_callback('/#hesapla\(([^)]+)\)#/', function ($matches) {
            return $this->resolveCalculation($matches[1]);
        }, $result);

        return $result;
    }

    protected function buildReplacements(Page $page, ?Article $article): void
    {
        $this->replacements = [
            'baslikisim' => $article?->title ?? $page->title,
            'yaziisim' => $article?->body ?? '',
            'yaziid' => (string) ($article?->id ?? ''),
            'strozet' => $article?->excerpt ?? $article?->meta_description ?? '',
            'strkategori' => $page->title,
            'stradmin' => $article?->author?->name ?? '',
            'strtarih' => $article?->published_at?->format('d.m.Y') ?? '',
            'strtarihday' => $article?->published_at?->format('d') ?? '',
            'strtarihmonth' => $article?->published_at?->format('m') ?? '',
            'strtarihyear' => $article?->published_at?->format('Y') ?? '',
            'strgtarih' => $article?->display_date ?? '',
            'strbilgi3' => $article?->extra_info ?? '',
            'str_bilgi3' => $article?->extra_info ?? '',
            'yazilink' => $article?->slug ? url($article->slug) : '',
            'slaytresim' => '',
            'buyukresim' => '',
            'kucukresim' => '',
            'sosyal' => '',
            'facebokyorum' => '',
        ];
    }

    protected function resolveCustomField(string $params): string
    {
        return '';
    }

    protected function resolveCalculation(string $params): string
    {
        $parts = explode(',', $params);
        if (count($parts) !== 3) {
            return '';
        }

        $val1 = floatval($parts[0]);
        $operation = trim($parts[1]);
        $val2 = floatval($parts[2]);

        return match ($operation) {
            '+' => (string) ($val1 + $val2),
            '-' => (string) ($val1 - $val2),
            '*' => (string) ($val1 * $val2),
            '/' => $val2 != 0 ? (string) ($val1 / $val2) : '0',
            '%' => $val2 != 0 ? (string) (($val1 / $val2) * 100) : '0',
            default => '',
        };
    }
}
