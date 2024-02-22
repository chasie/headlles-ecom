<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateBrandDiscountTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'brand_discount')) {
            Schema::create($this->prefix.'brand_discount', function (Blueprint $table) {
                $table->id();
                $table
                    ->foreignId('brand_id')
                    ->constrained($this->prefix.'brands')
                    ->cascadeOnDelete();
                $table
                    ->foreignId('discount_id')
                    ->constrained($this->prefix.'discounts')
                    ->cascadeOnDelete();
                $table
                    ->string('type', 20)
                    ->default('limitation');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'brand_discount');
    }
}
