<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateChannelablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->prefix.'channelables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained($this->prefix.'channels');
            $table->morphs('channelable');
            $table->boolean('enabled')->default(false);
            $table->datetime('published_at')->nullable();
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
        Schema::dropIfExists($this->prefix.'channelables');
    }
}
