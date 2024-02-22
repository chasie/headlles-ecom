<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateAssetsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'assets')) {
            Schema::create($this->prefix.'assets', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'assets');
    }
}
