<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateCustomerGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'customer_groups')) {
            Schema::create($this->prefix.'customer_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('handle')->unique();
                $table->boolean('default')->default(false)->index();
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
        Schema::dropIfExists($this->prefix.'customer_groups');
    }
}
