<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateCartsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'carts')) {
            Schema::create($this->prefix.'carts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->userForeignKey(nullable: true);
                $table
                    ->foreignId('customer_id')
                    ->nullable()
                    ->constrained($this->prefix.'customers');
                $table->foreignId('merged_id')->nullable()->constrained($this->prefix.'carts');
                $table->foreignId('currency_id')->constrained($this->prefix.'currencies');
                $table->foreignId('channel_id')->constrained($this->prefix.'channels');
                $table->foreignId('order_id')->nullable()->constrained($this->prefix.'orders');
                $table->string('coupon_code')->index()->nullable();
                $table->dateTime('completed_at')->nullable()->index();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'carts');
    }
}
