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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('mediable');
            $table->text('description')->nullable();
            $table->text('attribution')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('disk')->default(\App\Enums\StorageProvider::LOCAL->value);
            $table->string('path')->nullable();
            $table->double('size')->nullable();
            $table->boolean('current')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
