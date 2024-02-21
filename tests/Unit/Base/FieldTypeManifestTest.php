<?php

namespace HeadlessEcom\Tests\Unit\Base;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use HeadlessEcom\Base\FieldTypeManifest;
use HeadlessEcom\Base\FieldTypeManifestInterface;
use HeadlessEcom\Exceptions\FieldTypes\FieldTypeMissingException;
use HeadlessEcom\Exceptions\FieldTypes\InvalidFieldTypeException;
use HeadlessEcom\Models\Channel;
use HeadlessEcom\Tests\TestCase;

/**
 * @group core.fieldtype-manifest
 */
class FieldTypeManifestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_instantiate_class()
    {
        $manifest = app(FieldTypeManifestInterface::class);

        $this->assertInstanceOf(FieldTypeManifest::class, $manifest);
    }

    /** @test */
    public function can_return_types()
    {
        $manifest = app(FieldTypeManifestInterface::class);

        $this->assertInstanceOf(Collection::class, $manifest->getTypes());
    }

    /** @test */
    public function has_base_types_set()
    {
        $manifest = app(FieldTypeManifestInterface::class);

        $this->assertInstanceOf(Collection::class, $manifest->getTypes());

        $this->assertNotEmpty($manifest->getTypes());
    }

    /** @test */
    public function cannot_add_non_fieldtype()
    {
        $manifest = app(FieldTypeManifestInterface::class);

        $this->expectException(
            InvalidFieldTypeException::class
        );

        $manifest->add(Channel::class);

        $this->expectException(
            FieldTypeMissingException::class
        );

        $manifest->add(\HeadlessEcom\Models\Cart::class);
    }
}
