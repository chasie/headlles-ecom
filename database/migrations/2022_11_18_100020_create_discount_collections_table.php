<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateDiscountCollectionsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'collection_discount')) {
            Schema::create($this->prefix.'collection_discount', function (Blueprint $table) {
                $table->id();
                $table
                    ->foreignId('discount_id')
                    ->constrained($this->prefix.'discounts')
                    ->cascadeOnDelete();
                $table
                    ->foreignId('collection_id')
                    ->constrained($this->prefix.'collections')
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
        Schema::dropIfExists($this->prefix.'collection_discount');
    }
}
