<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('subscription_tiers', function (Blueprint $table) { // Тарифний план
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->timestamps();
        });

        Schema::create('ledger_transactions', function (Blueprint $table) { // реестр транзакций
            $table->id(); // это уже окончательно проведенная банком операция
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2); // Positive for deposit, negative for charge
            $table->string('type'); // 'deposit' or 'subscription_purchase'
            $table->timestamps();
        });

        Schema::create('subscription_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_tier_id')->constrained()->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }
};
