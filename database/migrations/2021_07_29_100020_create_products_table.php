<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'products')) {
            Schema::create($this->prefix.'products', function (Blueprint $table) {
                $table->id();
                $table
                    ->foreignId('brand_id')
                    ->nullable()
                    ->constrained($this->prefix.'brands');
                $table->foreignId('product_type_id')->constrained($this->prefix.'product_types');
                $table->string('status')->index();
                $table->json('attribute_data');
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists($this->prefix.'products');
    }
}
