<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'prices')) {
            Schema::create($this->prefix.'prices', function (Blueprint $table) {
                $table->id();
                $table
                    ->foreignId('customer_group_id')
                    ->nullable()
                    ->constrained($this->prefix.'customer_groups');
                $table
                    ->foreignId('currency_id')
                    ->nullable()
                    ->constrained($this->prefix.'currencies');
                $table->morphs('priceable');
                $table->unsignedBigInteger('price')->index();
                $table->unsignedBigInteger('compare_price')->nullable();
                $table->integer('tier')->default(1)->index();
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
        Schema::dropIfExists($this->prefix.'prices');
    }
}
