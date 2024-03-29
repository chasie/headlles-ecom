<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateCollectionProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'collection_product')) {
            Schema::create($this->prefix.'collection_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('collection_id')->constrained($this->prefix.'collections');
                $table->foreignId('product_id')->constrained($this->prefix.'products');
                $table->integer('position')->default(1)->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'collection_product');
    }
}
