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
        Schema::create('channel_videos', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('etag')->nullable();
            $table->string('kind')->nullable();
            $table->string('title')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('playlist_id')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->json('thumbnail')->nullable();
            $table->string('live_broadcast')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->default(\App\Enums\DocumentStatus::PENDING->value);

            $table->json('tag')->nullable();
            $table->integer('repost_count')->nullable();
            $table->string('resolution')->nullable();
            $table->string('playlist')->nullable();
            $table->string('playlist_index')->nullable();
            $table->bigInteger('view_count')->nullable();
            $table->string('duration')->nullable();
            $table->string('artist')->nullable();
            $table->text('filename')->nullable();

            $table->unsignedBigInteger('channel_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('channel_id')
                ->references('id')->on('channels')
                ->cascadeOnDelete();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_videos');
    }
};
