<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('order_name')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('total');
            $table->integer('base_total')->default(0);
            $table->integer('profit')->nullable();
            $table->string('payment_method');
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
