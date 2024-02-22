<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateAttributablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'attributables')) {
            Schema::create($this->prefix.'attributables', function (Blueprint $table) {
                $table->id();
                $table->morphs('attributable');
                $table->foreignId('attribute_id')->constrained($this->prefix.'attributes');
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
        Schema::dropIfExists($this->prefix.'attributables');
    }
}
