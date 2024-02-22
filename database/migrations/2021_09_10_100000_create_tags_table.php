<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateTagsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'tags')) {
            Schema::create($this->prefix.'tags', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('value')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'tags');
    }
}
