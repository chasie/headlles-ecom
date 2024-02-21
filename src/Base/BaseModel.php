<?php

namespace HeadlessEcom\Base;

use Illuminate\Database\Eloquent\Model;
use HeadlessEcom\Base\Traits\HasModelExtending;

abstract class BaseModel extends Model
{
    use HasModelExtending;

    /**
     * Create a new instance of the Model.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('headless-ecom.database.table_prefix').$this->getTable());

        if ($connection = config('headless-ecom.database.connection', false)) {
            $this->setConnection($connection);
        }
    }
}
