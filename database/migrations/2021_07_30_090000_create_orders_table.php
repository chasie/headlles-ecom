<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'orders')) {
            Schema::create(
                $this->prefix.'orders', function (Blueprint $table)
            {
                $table->bigIncrements('id');
                $table
                    ->foreignId('customer_id')
                    ->nullable()
                    ->constrained($this->prefix.'customers');
                $table->userForeignKey(nullable: true);
                $table
                    ->foreignId('cart_id')
                    ->nullable()
                    ->constrained($this->prefix.'carts')
                    ->nullOnDelete();
                $table->foreignId('channel_id')->constrained($this->prefix.'channels');
                $table->boolean('new_customer')->default(false)->index();
                $table->string('status')->index();
                $table->string('reference')->nullable()->unique();
                $table->string('customer_reference')->nullable();
                $table->unsignedBigInteger('sub_total')->unsigned()->index();
                $table->unsignedBigInteger('discount_total')->default(0)->unsigned()->index();
                $table->json('discount_breakdown')->nullable();
                $table->unsignedBigInteger('shipping_total')->default(0)->unsigned()->index();
                $table->json('shipping_breakdown')->nullable();
                $table->unsignedBigInteger('tax_total')->unsigned()->index();
                $table->json('tax_breakdown');
                $table->unsignedBigInteger('total')->unsigned()->index();
                $table->text('notes')->nullable();
                $table->string('currency_code', 3);
                $table->string('compare_currency_code', 3)->nullable();
                $table->decimal('exchange_rate', 10, 4)->default(1);
                $table->dateTime('placed_at')->nullable()->index();
                $table->json('meta')->nullable();
                $table->timestamps();
            }
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'orders');
    }
}
