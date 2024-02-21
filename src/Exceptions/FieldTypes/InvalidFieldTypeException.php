<?php

namespace HeadlessEcom\Exceptions\FieldTypes;

use Exception;

class InvalidFieldTypeException extends Exception
{
    public function __construct($classname)
    {
        $this->message = __('headless-ecom::exceptions.invalid_fieldtype', [
            'class' => $classname,
        ]);
    }
}
