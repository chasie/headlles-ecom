<?php

namespace HeadlessEcom\Exceptions\FieldTypes;

use Exception;

class FieldTypeMissingException extends Exception
{
    public function __construct($classname)
    {
        $this->message = __('headless-ecom::exceptions.fieldtype_missing', [
            'class' => $classname,
        ]);
    }
}
