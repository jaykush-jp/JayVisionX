<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Encrypted key/value store for third-party API credentials (AI providers,
 * Google, Meta). Values are encrypted at the model layer (App\Models\Credential)
 * so secrets never sit in plain text or in .env. Admins manage these from
 * Admin → API Credentials.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();       // encrypted ciphertext
            $table->boolean('is_active')->default(true); // disable a key without deleting it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
