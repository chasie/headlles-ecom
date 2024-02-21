<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateCustomerUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->prefix.'customer_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained($this->prefix.'customers');
            $table->userForeignKey();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'customer_user');
    }
}
