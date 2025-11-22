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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo_path')->nullable()->after('email_verified_at');
            $table->string('identification', '8')->unique()->after('name');
            $table->string('email')->nullable()->change();
            $table->enum('role', ['admin', 'user'])->default('user')->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->dropColumn('identification');
            $table->dropColumn('role');
            $table->dropColumn('profile_photo_path');
        });
    }
};
