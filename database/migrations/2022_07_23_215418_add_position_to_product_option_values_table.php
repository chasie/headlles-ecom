<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class AddPositionToProductOptionValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table($this->prefix.'product_option_values', function (Blueprint $table) {
            $table->integer('position')->after('name')->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table($this->prefix.'product_option_values', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}
