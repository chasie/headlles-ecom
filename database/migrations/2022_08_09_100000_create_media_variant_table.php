<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateMediaVariantTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'media_product_variant')) {
            Schema::create($this->prefix.'media_product_variant', function (Blueprint $table) {
                $table->id();
                $table->foreignId('media_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_variant_id')->constrained($this->prefix.'product_variants')->onDelete('cascade');
                $table->boolean('primary')->default(false)->index();
                $table->smallInteger('position')->default(1)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'media_product_variant');
    }
}
