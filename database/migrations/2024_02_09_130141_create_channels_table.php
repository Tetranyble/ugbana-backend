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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('channel_user_id')->nullable();
            $table->string('etag')->nullable();
            $table->string('title')->nullable();
            $table->string('kind')->nullable();
            $table->string('country')->nullable();
            $table->string('custom_url')->nullable();
            $table->string('language')->nullable();
            $table->string('url')->nullable();
            $table->string('subscriber_count')->nullable();
            $table->text('description')->nullable();
            $table->json('thumbnail')->nullable();
            $table->boolean('is_owner')->default(false);
            $table->boolean('is_viable')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
