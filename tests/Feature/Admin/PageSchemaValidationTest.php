<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Language;
use App\Models\Page;
use App\Models\SectionTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSchemaValidationTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected Language $language;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin    = Admin::factory()->create();
        $this->language = Language::factory()->create();
    }

    private function makeSectionsJson(int $templateId, array $content = []): string
    {
        return json_encode([
            'version' => 2,
            'regions' => [
                'body' => [[
                    'id'      => 'row_body_1',
                    'type'    => 'row',
                    'columns' => [[
                        'id'     => 'col_body_1_1',
                        'width'  => 12,
                        'blocks' => [[
                            'id'                  => 'block_1',
                            'type'                => 'hero',
                            'section_template_id' => $templateId,
                            'content'             => $content,
                            'is_active'           => true,
                        ]],
                    ]],
                ]],
            ],
        ]);
    }

    public function test_required_field_missing_causes_422(): void
    {
        $template = SectionTemplate::factory()->create([
            'schema_json' => [
                'title' => ['type' => 'string', 'required' => true, 'label' => 'Başlık'],
            ],
        ]);

        $page = Page::factory()->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title'       => $page->title,
                'language_id' => $this->language->id,
                'status'      => 'draft',
                'sections_json' => $this->makeSectionsJson($template->id, ['title' => '']),
            ]);

        $response->assertStatus(302); // redirected back with errors
        $response->assertSessionHasErrors();

        $errors = session('errors')->all();
        $this->assertNotEmpty($errors);
        $this->assertTrue(
            collect($errors)->contains(fn ($e) => str_contains($e, 'Başlık') && str_contains($e, 'zorunlu')),
            'Expected a "Başlık zorunludur" error, got: ' . implode(', ', $errors)
        );
    }

    public function test_valid_content_passes_validation(): void
    {
        $template = SectionTemplate::factory()->create([
            'schema_json' => [
                'title' => ['type' => 'string', 'required' => true, 'label' => 'Başlık'],
            ],
        ]);

        $page = Page::factory()->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title'         => $page->title,
                'language_id'   => $this->language->id,
                'status'        => 'draft',
                'sections_json' => $this->makeSectionsJson($template->id, ['title' => 'Hero başlığı']),
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_url_field_validates_format(): void
    {
        $template = SectionTemplate::factory()->create([
            'schema_json' => [
                'button_url' => ['type' => 'url', 'required' => false, 'label' => 'Buton URL'],
            ],
        ]);

        $page = Page::factory()->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title'         => $page->title,
                'language_id'   => $this->language->id,
                'status'        => 'draft',
                'sections_json' => $this->makeSectionsJson($template->id, ['button_url' => 'gecersiz-url']),
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $errors = session('errors')->all();
        $this->assertTrue(
            collect($errors)->contains(fn ($e) => str_contains($e, 'URL')),
            'Expected a URL format error, got: ' . implode(', ', $errors)
        );
    }

    public function test_empty_sections_json_skips_schema_validation(): void
    {
        $page = Page::factory()->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title'         => $page->title,
                'language_id'   => $this->language->id,
                'status'        => 'draft',
                'sections_json' => null,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_template_without_schema_skips_validation(): void
    {
        $template = SectionTemplate::factory()->create([
            'schema_json' => [],
        ]);

        $page = Page::factory()->create(['language_id' => $this->language->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title'         => $page->title,
                'language_id'   => $this->language->id,
                'status'        => 'draft',
                'sections_json' => $this->makeSectionsJson($template->id, []),
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }
}
