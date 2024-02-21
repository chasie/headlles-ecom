<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class AddAttributesToBrandsTable extends Migration
{
    public function up(): void
    {
        Schema::table($this->prefix.'brands', function (Blueprint $table) {
            $table->json('attribute_data')->after('name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table($this->prefix.'brands', function ($table) {
            $table->dropColumn('attribute_data');
        });
    }
}
