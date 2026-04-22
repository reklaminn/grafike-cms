<?php

namespace Tests\Unit\Support;

use App\Support\FrontendSections;
use PHPUnit\Framework\TestCase;

class FrontendSectionsTest extends TestCase
{
    public function test_flat_sections_are_normalized_into_body_rows(): void
    {
        $sections = [[
            'id' => 'hero_1',
            'type' => 'hero',
            'variation' => 'porto-split',
            'render_mode' => 'html',
            'section_template_id' => 12,
            'content' => [
                'title' => 'Modern Furniture Collections',
            ],
        ]];

        $normalized = FrontendSections::normalize($sections);

        $this->assertSame(2, $normalized['version']);
        $this->assertCount(1, $normalized['regions']['body']);
        $this->assertSame('hero', $normalized['regions']['body'][0]['columns'][0]['blocks'][0]['type']);
        $this->assertSame(12, $normalized['regions']['body'][0]['columns'][0]['blocks'][0]['section_template_id']);
    }

    public function test_flatten_blocks_reads_region_based_structure(): void
    {
        $sections = [
            'version' => 2,
            'regions' => [
                'header' => [],
                'body' => [[
                    'id' => 'row_body_1',
                    'type' => 'row',
                    'columns' => [[
                        'id' => 'col_body_1',
                        'width' => 12,
                        'blocks' => [[
                            'id' => 'block_hero_1',
                            'type' => 'hero',
                            'variation' => 'porto-split',
                            'render_mode' => 'html',
                            'section_template_id' => 9,
                            'is_active' => true,
                            'content' => [
                                'title' => 'Hero title',
                            ],
                        ]],
                    ]],
                ]],
                'footer' => [],
            ],
        ];

        $blocks = FrontendSections::flattenBlocks($sections);

        $this->assertCount(1, $blocks);
        $this->assertSame('body', $blocks[0]['region']);
        $this->assertSame('col_body_1', $blocks[0]['column_id']);
        $this->assertSame(12, $blocks[0]['column_width']);
        $this->assertSame('hero', $blocks[0]['type']);
    }
}
