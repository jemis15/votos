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
        Schema::create('votes', function (Blueprint $table) {
            $table->foreignId('voter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('election_id')->constrained()->onDelete('cascade');

            $table->foreignId('candidate_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->primary(['voter_id', 'election_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
