<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Base\Migration;

class CreateTransactionsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable($this->prefix.'transactions')) {
            Schema::create($this->prefix.'transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table
                    ->foreignId('parent_transaction_id')
                    ->nullable()
                    ->constrained($this->prefix.'transactions');
                $table->foreignId('order_id')->constrained($this->prefix.'orders');
                $table->boolean('success')->index();
                $table
                    ->enum('type', ['refund', 'intent', 'capture'])
                    ->index()
                    ->default('capture');
                $table->string('driver');
                $table->integer('amount')->unsigned()->index();
                $table->string('reference')->index();
                $table->string('status');
                $table->string('notes')->nullable();
                $table->string('card_type', 25)->index();
                $table->string('last_four', 4);
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->dateTime('captured_at')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix.'transactions');
    }
}
