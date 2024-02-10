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
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->nullable();
            $table->string('name');
            $table->string('color');
            $table->string('timezone');
            $table->boolean('is_primary')->default(false);

            $table->unsignedBigInteger('web_service_id');

            $table->foreign('web_service_id')
                ->references('id')
                ->on('web_services')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
