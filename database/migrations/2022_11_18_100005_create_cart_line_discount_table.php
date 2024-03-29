<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateCartLineDiscountTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'cart_line_discount')) {
            Schema::create($this->prefix.'cart_line_discount', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cart_line_id')->constrained($this->prefix.'carts')->cascadeOnDelete();
                $table->foreignId('discount_id')->constrained($this->prefix.'discounts')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'cart_line_discount');
    }
}
