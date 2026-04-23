<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Collection;

class FrontendSections
{
    public static function emptyStructure(): array
    {
        return [
            'version' => 2,
            'regions' => [
                'header' => [],
                'body' => [],
                'footer' => [],
            ],
        ];
    }

    public static function normalize(array|null $sections): array
    {
        if (empty($sections)) {
            return static::emptyStructure();
        }

        if (isset($sections['regions']) && is_array($sections['regions'])) {
            return [
                'version' => (int) ($sections['version'] ?? 2),
                'regions' => [
                    'header' => array_values(collect($sections['regions']['header'] ?? [])->map(
                        fn (array $row, int $index) => static::normalizeRow($row, 'header', $index)
                    )->all()),
                    'body' => array_values(collect($sections['regions']['body'] ?? [])->map(
                        fn (array $row, int $index) => static::normalizeRow($row, 'body', $index)
                    )->all()),
                    'footer' => array_values(collect($sections['regions']['footer'] ?? [])->map(
                        fn (array $row, int $index) => static::normalizeRow($row, 'footer', $index)
                    )->all()),
                ],
            ];
        }

        $bodyRows = collect(array_values($sections))
            ->map(function (array $section, int $index) {
                return [
                    'id' => 'row_body_'.($index + 1),
                    'type' => 'row',
                    'is_active' => true,
                    'columns' => [[
                        'id' => 'col_body_'.($index + 1),
                        'width' => 12,
                        'is_active' => true,
                        'blocks' => [static::normalizeBlock($section, $index)],
                    ]],
                ];
            })
            ->all();

        return [
            'version' => 2,
            'regions' => [
                'header' => [],
                'body' => $bodyRows,
                'footer' => [],
            ],
        ];
    }

    public static function flattenBlocks(array|null $sections): array
    {
        $normalized = static::normalize($sections);
        $blocks = [];

        foreach ($normalized['regions'] as $region => $rows) {
            foreach ($rows as $rowIndex => $row) {
                foreach (($row['columns'] ?? []) as $columnIndex => $column) {
                    foreach (($column['blocks'] ?? []) as $blockIndex => $block) {
                        $block['region'] = $region;
                        $block['row_id'] = $row['id'] ?? 'row_'.$region.'_'.($rowIndex + 1);
                        $block['column_id'] = $column['id'] ?? 'col_'.$region.'_'.($columnIndex + 1);
                        $block['column_width'] = $column['width'] ?? 12;
                        $block['is_active'] = $block['is_active'] ?? true;
                        $block['sort_order'] = $block['sort_order'] ?? ($blockIndex + 1);
                        $blocks[] = $block;
                    }
                }
            }
        }

        return $blocks;
    }

    public static function mapBlocks(array|null $sections, Closure $callback): array
    {
        $normalized = static::normalize($sections);

        foreach ($normalized['regions'] as $region => &$rows) {
            foreach ($rows as &$row) {
                foreach ($row['columns'] as &$column) {
                    $column['blocks'] = collect($column['blocks'] ?? [])
                        ->map(fn (array $block) => $callback($block, $region))
                        ->all();
                }
                unset($column);
            }
            unset($row);
        }
        unset($rows);

        return $normalized;
    }

    public static function collectTemplateIds(array|null $sections): Collection
    {
        return collect(static::flattenBlocks($sections))
            ->pluck('section_template_id')
            ->filter()
            ->values();
    }

    public static function containsType(array|null $sections, string $type): bool
    {
        return collect(static::flattenBlocks($sections))
            ->contains(fn (array $block) => ($block['type'] ?? null) === $type);
    }

    protected static function normalizeBlock(array $section, int $index): array
    {
        return [
            'id' => $section['id'] ?? ('block_'.($section['type'] ?? 'section').'_'.($index + 1)),
            'type' => $section['type'] ?? 'unknown',
            'variation' => $section['variation'] ?? 'default',
            'render_mode' => $section['render_mode'] ?? 'html',
            'section_template_id' => $section['section_template_id'] ?? null,
            'component_key' => $section['component_key'] ?? null,
            'template_name' => $section['template_name'] ?? null,
            'html_template' => $section['html_template'] ?? null,
            'schema' => $section['schema'] ?? [],
            'content' => $section['content'] ?? [],
            'is_active' => $section['is_active'] ?? true,
            'sort_order' => $section['sort_order'] ?? ($index + 1),
            'wrapper_tag' => $section['wrapper_tag'] ?? 'section',
            'css_class' => $section['css_class'] ?? null,
            'element_id' => $section['element_id'] ?? null,
            'inline_style' => $section['inline_style'] ?? null,
            'custom_attributes' => $section['custom_attributes'] ?? null,
            'html_override' => $section['html_override'] ?? null,
        ];
    }

    protected static function normalizeRow(array $row, string $region, int $rowIndex): array
    {
        return [
            'id' => $row['id'] ?? 'row_'.$region.'_'.($rowIndex + 1),
            'type' => 'row',
            'is_active' => $row['is_active'] ?? true,
            'container' => $row['container'] ?? 'container',
            'wrapper_tag' => $row['wrapper_tag'] ?? 'section',
            'css_class' => $row['css_class'] ?? null,
            'element_id' => $row['element_id'] ?? null,
            'inline_style' => $row['inline_style'] ?? null,
            'custom_attributes' => $row['custom_attributes'] ?? null,
            'columns' => collect($row['columns'] ?? [])
                ->map(fn (array $column, int $columnIndex) => static::normalizeColumn($column, $region, $rowIndex, $columnIndex))
                ->values()
                ->all(),
        ];
    }

    protected static function normalizeColumn(array $column, string $region, int $rowIndex, int $columnIndex): array
    {
        return [
            'id' => $column['id'] ?? 'col_'.$region.'_'.($rowIndex + 1).'_'.($columnIndex + 1),
            'width' => (int) ($column['width'] ?? 12),
            'is_active' => $column['is_active'] ?? true,
            'responsive' => [
                'xs' => $column['responsive']['xs'] ?? $column['width'] ?? 12,
                'sm' => $column['responsive']['sm'] ?? null,
                'md' => $column['responsive']['md'] ?? null,
                'lg' => $column['responsive']['lg'] ?? null,
                'xl' => $column['responsive']['xl'] ?? null,
            ],
            'css_class' => $column['css_class'] ?? null,
            'element_id' => $column['element_id'] ?? null,
            'inline_style' => $column['inline_style'] ?? null,
            'custom_attributes' => $column['custom_attributes'] ?? null,
            'blocks' => collect($column['blocks'] ?? [])
                ->map(fn (array $block, int $blockIndex) => static::normalizeBlock($block, $blockIndex))
                ->values()
                ->all(),
        ];
    }
}
