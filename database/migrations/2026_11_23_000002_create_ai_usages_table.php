<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-call AI usage log, used by Admin → AI Insights to estimate spend across
 * providers and models.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usages', function (Blueprint $table) {
            $table->id();
            $table->string('provider');                 // gemini|openai|anthropic|...
            $table->string('model')->nullable();
            $table->string('kind')->default('text');     // text|image
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->unsignedInteger('images')->default(0);
            $table->decimal('cost', 12, 6)->default(0);   // estimated USD
            $table->timestamps();

            $table->index(['provider', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usages');
    }
};
