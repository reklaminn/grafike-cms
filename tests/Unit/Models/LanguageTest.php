<?php

namespace Tests\Unit\Models;

use App\Models\Language;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_language(): void
    {
        $language = Language::factory()->create([
            'name' => 'Türkçe',
            'code' => 'tr',
            'locale' => 'tr_TR',
        ]);

        $this->assertDatabaseHas('languages', [
            'name' => 'Türkçe',
            'code' => 'tr',
        ]);
    }

    public function test_active_scope(): void
    {
        Language::factory()->create(['is_active' => true]);
        Language::factory()->create(['is_active' => true]);
        Language::factory()->create(['is_active' => false]);

        $this->assertCount(2, Language::active()->get());
    }

    public function test_language_has_pages(): void
    {
        $language = Language::factory()->create();
        Page::factory()->count(3)->create(['language_id' => $language->id]);

        $this->assertCount(3, $language->pages);
    }

    public function test_is_active_cast(): void
    {
        $language = Language::factory()->create(['is_active' => 1]);

        $language->refresh();
        $this->assertTrue($language->is_active);
    }
}
