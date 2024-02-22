<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateCartLinesTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'cart_lines')) {
            Schema::create($this->prefix.'cart_lines', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('cart_id')->constrained($this->prefix.'carts');
                $table->morphs('purchasable');
                $table->unsignedInteger('quantity');
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'cart_lines');
    }
}
