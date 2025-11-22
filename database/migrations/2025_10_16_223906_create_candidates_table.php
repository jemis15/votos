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
        Schema::create('candidates', function (Blueprint $table) {
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('events')->cascadeOnDelete();

            $table->foreignId('cargo_id')->nullable()->constrained();

            $table->primary(['candidate_id', 'room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
