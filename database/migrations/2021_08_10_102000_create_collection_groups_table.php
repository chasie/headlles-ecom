<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateCollectionGroupsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'collection_groups')) {
            Schema::create($this->prefix.'collection_groups', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('handle')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'collection_groups');
    }
}
