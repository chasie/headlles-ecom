<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateProductOptionsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'product_options')) {
            Schema::create($this->prefix.'product_options', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->json('name');
                $table->json('label')->nullable();
                $table->string('handle')->unique()->nullable();
                $table->integer('position')->default(0)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'product_options');
    }
}
