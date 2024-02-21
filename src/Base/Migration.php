<?php

namespace HeadlessEcom\Base;

use Illuminate\Database\Migrations\Migration as BaseMigration;

abstract class Migration extends BaseMigration
{
    /**
     * Migration table prefix.
     */
    protected string $prefix = '';

    /**
     * Create a new instance of the migration.
     */
    public function __construct()
    {
        $this->prefix = config('headless-ecom.database.table_prefix');
    }

    /**
     * Use the connection specified in config.
     *
     * @return void
     */
    public function getConnection()
    {
        if ($connection = config('headless-ecom.database.connection', false)) {
            return $connection;
        }

        return parent::getConnection();
    }
}
