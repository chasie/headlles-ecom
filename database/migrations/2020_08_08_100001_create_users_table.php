<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'users')) {
            Schema::create($this->prefix.'users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'brands');
    }
}
