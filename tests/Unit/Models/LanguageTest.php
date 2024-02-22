<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Url;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.models
 */
class LanguageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_a_language()
    {
        $language = Language::factory()->create([
            'code' => 'fr',
            'name' => 'Français',
            'default' => true,
        ]);

        $this->assertEquals('fr', $language->code);
        $this->assertEquals('Français', $language->name);
        $this->assertTrue($language->default);
    }

    /** @test */
    public function can_cleanup_relations_on_deletion()
    {
        $language = Language::factory()->create([
            'code' => 'fr',
            'name' => 'Français',
            'default' => true,
        ]);

        Url::factory()->create([
            'language_id' => $language->id,
        ]);

        $this->assertDatabaseHas((new Url)->getTable(), [
            'language_id' => $language->id,
        ]);

        $language->delete();

        $this->assertDatabaseMissing((new Url)->getTable(), [
            'language_id' => $language->id,
        ]);
    }
}
