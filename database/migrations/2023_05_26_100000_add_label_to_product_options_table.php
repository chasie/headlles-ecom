<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class AddLabelToProductOptionsTable extends Migration
{
    public function up(): void
    {
        Schema::table($this->prefix.'product_options', function (Blueprint $table) {
            $table->json('label')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table($this->prefix.'product_options', function ($table) {
            $table->dropColumn('label');
        });
    }
}
