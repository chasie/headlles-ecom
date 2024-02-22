<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateTaxClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'tax_classes')) {
            Schema::create($this->prefix.'tax_classes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table
                    ->boolean('default')
                    ->default(false)
                    ->index();
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
        Schema::dropIfExists($this->prefix.'tax_classes');
    }
}
