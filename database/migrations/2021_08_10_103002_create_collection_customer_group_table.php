<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateCollectionCustomerGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'collection_customer_group')) {
            Schema::create($this->prefix.'collection_customer_group', function (Blueprint $table) {
                $table->id();
                $table->foreignId('collection_id')->constrained($this->prefix.'collections');
                $table->foreignId('customer_group_id')->constrained($this->prefix.'customer_groups');
                $table->scheduling();
                $table->boolean('visible')->default(true)->index();
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
        Schema::dropIfExists($this->prefix.'collection_customer_group');
    }
}
