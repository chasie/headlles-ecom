<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateTaxZoneStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'tax_zone_states')) {
            Schema::create($this->prefix.'tax_zone_states', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tax_zone_id')->nullable()->constrained($this->prefix.'tax_zones');
                $table->foreignId('state_id')->nullable()->constrained($this->prefix.'states');
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
        Schema::dropIfExists($this->prefix.'tax_zone_states');
    }
}
