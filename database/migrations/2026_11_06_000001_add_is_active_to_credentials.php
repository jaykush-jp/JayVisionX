<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-credential active flag. Admins can disable an API key/credential without
 * deleting it (the encrypted value is kept but treated as "not configured"
 * while inactive).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
