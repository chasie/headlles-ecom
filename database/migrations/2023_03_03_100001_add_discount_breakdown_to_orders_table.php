<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class AddDiscountBreakdownToOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::table($this->prefix.'orders', function (Blueprint $table) {
            $table->json('discount_breakdown')->nullable()->after('sub_total');
        });
    }

    public function down(): void
    {
        Schema::table($this->prefix.'orders', function ($table) {
            $table->dropColumn('discount_breakdown');
        });
    }
}
