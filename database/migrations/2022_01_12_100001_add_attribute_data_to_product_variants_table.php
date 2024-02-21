<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class AddAttributeDataToProductVariantsTable extends Migration
{
    public function up()
    {
        Schema::table($this->prefix.'product_variants', function (Blueprint $table) {
            $table->json('attribute_data')->after('tax_class_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table($this->prefix.'product_variants', function (Blueprint $table) {
            $table->dropColumn('attribute_data');
        });
    }
}
