<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateAttributeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'attribute_groups')) {
            Schema::create($this->prefix.'attribute_groups', function (Blueprint $table) {
                $table->id();
                $table->string('attributable_type')->index();
                $table->json('name');
                $table->string('handle')->unique();
                $table->integer('position')->index();
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
        Schema::dropIfExists($this->prefix.'attribute_groups');
    }
}
