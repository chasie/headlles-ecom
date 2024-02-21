<?php

namespace HeadlessEcom\Facades;

use Illuminate\Support\Facades\DB as DBFacade;

class DB extends DBFacade
{
    /**
     * Get the registered DatabaseManger class.
     *
     * @return \HeadlessEcom\Managers\DatabaseManager
     */
    public static function connection()
    {
        // return custom connection
        return parent::connection(config('headless-ecom.database.connection'));
    }
}
