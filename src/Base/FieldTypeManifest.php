<?php

namespace HeadlessEcom\Base;

use HeadlessEcom\Exceptions\FieldTypes\FieldTypeMissingException;
use HeadlessEcom\Exceptions\FieldTypes\InvalidFieldTypeException;
use HeadlessEcom\FieldTypes\Dropdown;
use HeadlessEcom\FieldTypes\File;
use HeadlessEcom\FieldTypes\ListField;
use HeadlessEcom\FieldTypes\Number;
use HeadlessEcom\FieldTypes\Text;
use HeadlessEcom\FieldTypes\Toggle;
use HeadlessEcom\FieldTypes\TranslatedText;
use HeadlessEcom\FieldTypes\YouTube;

class FieldTypeManifest
{
    /**
     * The FieldTypes available in HeadlessEcom.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $fieldTypes;

    public function __construct()
    {
        $this->fieldTypes = collect([
            Dropdown::class,
            ListField::class,
            Number::class,
            Text::class,
            Toggle::class,
            TranslatedText::class,
            YouTube::class,
            File::class,
        ]);
    }

    /**
     * Add a FieldType into HeadlessEcom.
     *
     * @param  string  $classname
     * @return void
     */
    public function add($classname)
    {
        if (! class_exists($classname)) {
            throw new FieldTypeMissingException($classname);
        }

        if (! (app()->make($classname) instanceof FieldType)) {
            throw new InvalidFieldTypeException($classname);
        }

        $this->fieldTypes->push($classname);
    }

    /**
     * Return the fieldtypes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTypes()
    {
        return $this->fieldTypes->map(fn ($type) => app()->make($type));
    }
}
