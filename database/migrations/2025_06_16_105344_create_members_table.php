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

        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('package_id')->nullable()->constrained();
            $table->foreignId('personal_trainer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('social_media');
            $table->string('phone', 15);
            $table->string('address');
            $table->enum('gender', ["M","F"])->default('M');
            $table->date('joined');
            $table->date('expired')->nullable();

            $table->uuid('qr_code')->unique();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
