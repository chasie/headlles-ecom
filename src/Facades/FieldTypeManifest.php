<?php

namespace HeadlessEcom\Facades;

use Illuminate\Support\Facades\Facade;
use HeadlessEcom\Base\FieldTypeManifestInterface;

class FieldTypeManifest extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return FieldTypeManifestInterface::class;
    }
}
