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
        Schema::create('web_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->json('token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->json('scopes')->nullable();
            $table->string('client_id')->nullable(); //google_id that's account ID
            $table->string('provider')->default(\App\Enums\StorageProvider::GOOGLE->value);
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_services');
    }
};
