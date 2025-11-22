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
        Schema::create('eligibles', function (Blueprint $table) {
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();

            $table->primary(['candidate_id', 'election_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligibles');
    }
};
