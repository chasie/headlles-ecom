<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateTaggablesTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'taggables')) {
            Schema::create($this->prefix.'taggables', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('tag_id')->constrained($this->prefix.'tags');
                $table->morphs('taggable');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'taggables');
    }
}
