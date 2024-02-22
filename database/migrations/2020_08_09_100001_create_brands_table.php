<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateBrandsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'brands')) {
            Schema::create($this->prefix.'brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->json('attribute_data')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'brands');
    }
}
