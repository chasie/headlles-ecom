<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateTaxRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'tax_rates')) {
            Schema::create($this->prefix.'tax_rates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tax_zone_id')->nullable()->constrained($this->prefix.'tax_zones');
                $table->tinyInteger('priority')->default(1)->index()->unsigned();
                $table->string('name');
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
        Schema::dropIfExists($this->prefix.'tax_rates');
    }
}
