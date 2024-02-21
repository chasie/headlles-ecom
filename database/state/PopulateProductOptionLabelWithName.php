<?php

namespace HeadlessEcom\Database\State;

use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\Models\ProductOption;

class PopulateProductOptionLabelWithName
{
    public function prepare()
    {
        //
    }

    public function run()
    {
        if (! $this->canRun() || ! $this->shouldRun()) {
            return;
        }

        DB::transaction(function () {
            ProductOption::where('label', '')
                ->orWhereNull('label')
                ->update([
                    'label' => DB::raw('name'),
                ]);
        });
    }

    protected function canRun()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return Schema::hasTable("{$prefix}product_options");
    }

    protected function shouldRun()
    {
        return ProductOption::whereJsonLength('label', 0)->count() > 0;
    }
}
