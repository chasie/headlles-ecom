<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->prefix.'states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained($this->prefix.'countries');
            $table->string('name');
            $table->string('code');
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
        Schema::dropIfExists($this->prefix.'states');
    }
}
