<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class SetSectionToNullableOnAttributes extends Migration
{
    public function up()
    {
        Schema::table($this->prefix.'attributes', function (Blueprint $table) {
            $table->string('section')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table($this->prefix.'attributes', function ($table) {
            $table->string('section')->nullable(false)->change();
        });
    }
}
