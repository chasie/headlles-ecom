<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateProductAssociationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'product_associations')) {
            Schema::create($this->prefix.'product_associations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_parent_id')->constrained($this->prefix.'products');
                $table->foreignId('product_target_id')->constrained($this->prefix.'products');
                $table->string('type')->index();
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
        Schema::dropIfExists($this->prefix.'product_associations');
    }
}
