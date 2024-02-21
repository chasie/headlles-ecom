<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;
use HeadlessEcom\Facades\DB;

class AddBrandIdToProductsTable extends Migration
{
    public function up(): void
    {
        Schema::table($this->prefix.'products', function (Blueprint $table) {
            $table->foreignId('brand_id')->after('id')
                ->nullable()
                ->constrained($this->prefix.'brands');
        });

        Schema::table($this->prefix.'products', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
    }

    public function down(): void
    {
        Schema::table($this->prefix.'products', function ($table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['brand_id']);
            }
            $table->dropColumn('brand_id');
        });
    }
}
