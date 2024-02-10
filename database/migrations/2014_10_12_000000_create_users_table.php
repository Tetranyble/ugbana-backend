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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('suspend_at')->nullable();
            $table->boolean('is_accept_condition')->default(false);
            $table->string('status')->default(\App\Enums\UserStatus::ACTIVE->value);
            $table->string('password');

            $table->unsignedBigInteger('referrer_id')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('referrer_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
