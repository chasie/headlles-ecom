<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class AddShippingBreakdownToOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::table($this->prefix.'orders', function (Blueprint $table) {
            $table->json('shipping_breakdown')->nullable()->after('discount_total');
        });
    }

    public function down(): void
    {
        Schema::table($this->prefix.'orders', function ($table) {
            $table->dropColumn('shipping_breakdown');
        });
    }
}
