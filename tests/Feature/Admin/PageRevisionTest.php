<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageRevision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRevisionTest extends TestCase
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

    public function test_updating_sections_json_creates_revision(): void
    {
        $original = ['version' => 2, 'regions' => ['body' => [], 'header' => [], 'footer' => []]];

        $page = Page::factory()->create([
            'language_id'   => $this->language->id,
            'sections_json' => $original,
        ]);

        $this->assertDatabaseCount('page_revisions', 0);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title'         => $page->title,
                'language_id'   => $this->language->id,
                'status'        => $page->status,
                'sections_json' => json_encode(['version' => 2, 'regions' => ['body' => [['id' => 'row_1']], 'header' => [], 'footer' => []]]),
            ]);

        $this->assertDatabaseCount('page_revisions', 1);

        $revision = PageRevision::first();
        $this->assertEquals($page->id, $revision->page_id);
        $this->assertEquals('pre-update', $revision->reason);
        $this->assertEquals($original, $revision->snapshot['sections_json']);
    }

    public function test_no_revision_when_sections_json_unchanged(): void
    {
        $sections = ['version' => 2, 'regions' => ['body' => [], 'header' => [], 'footer' => []]];

        $page = Page::factory()->create([
            'language_id'   => $this->language->id,
            'sections_json' => $sections,
        ]);

        // Update only the title — sections_json unchanged
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title'         => 'Yeni Başlık',
                'language_id'   => $this->language->id,
                'status'        => $page->status,
                'sections_json' => json_encode($sections),
            ]);

        $this->assertDatabaseCount('page_revisions', 0);
    }

    public function test_restore_revision_reverts_sections_json(): void
    {
        $original = ['version' => 2, 'regions' => ['body' => [['id' => 'original_row']], 'header' => [], 'footer' => []]];
        $updated  = ['version' => 2, 'regions' => ['body' => [['id' => 'new_row']], 'header' => [], 'footer' => []]];

        $page = Page::factory()->create([
            'language_id'   => $this->language->id,
            'sections_json' => $original,
        ]);

        // Save update (creates revision of $original)
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.pages.update', $page), [
                'title'         => $page->title,
                'language_id'   => $this->language->id,
                'status'        => $page->status,
                'sections_json' => json_encode($updated),
            ]);

        $revision = PageRevision::where('page_id', $page->id)->first();
        $this->assertNotNull($revision);

        // Restore
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.pages.restore-revision', [$page, $revision]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $page->refresh();
        $this->assertEquals($original, $page->sections_json);
    }

    public function test_restore_revision_belongs_to_page(): void
    {
        $page1 = Page::factory()->create(['language_id' => $this->language->id]);
        $page2 = Page::factory()->create(['language_id' => $this->language->id]);

        $revision = PageRevision::create([
            'page_id'    => $page1->id,
            'snapshot'   => ['sections_json' => null, 'layout_json' => null],
            'reason'     => 'test',
            'created_at' => now(),
        ]);

        // Try to restore page1's revision via page2's route → 404
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.pages.restore-revision', [$page2, $revision]));

        $response->assertStatus(404);
    }
}
