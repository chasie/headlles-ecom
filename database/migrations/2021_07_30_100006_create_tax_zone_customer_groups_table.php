<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateTaxZoneCustomerGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'tax_zone_customer_groups')) {
            Schema::create($this->prefix.'tax_zone_customer_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tax_zone_id')->nullable()->constrained($this->prefix.'tax_zones');
                $table->foreignId('customer_group_id')->nullable()->constrained($this->prefix.'customer_groups');
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
        Schema::dropIfExists($this->prefix.'tax_zone_customer_groups');
    }
}
