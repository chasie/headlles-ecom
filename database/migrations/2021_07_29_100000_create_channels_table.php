<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'channels')) {
            Schema::create($this->prefix.'channels', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('handle')->unique();
                $table->boolean('default')->default(0)->index();
                $table->string('url')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists($this->prefix.'channels');
    }
}
