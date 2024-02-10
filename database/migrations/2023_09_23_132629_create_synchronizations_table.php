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
        Schema::create('synchronizations', function (Blueprint $table) {
            $table->string('id');
            $table->nullableMorphs('synchronizable', 'google_synchronizations_type_id_index');
            $table->string('token')->nullable();
            $table->string('resource_id')->nullable();
            $table->datetime('expired_at')->nullable();
            $table->datetime('last_synchronized_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('synchronizations');
    }
};
