<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase C — configurable automation rules (+ execution logs) and the
 * deterministic quote builder's stored quote requests.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger');        // chat_started|lead_created|handover_requested|quote_requested|low_confidence
            $table->string('action');         // notify_admin|auto_message|set_pending
            $table->text('message')->nullable();       // for auto_message / notify body
            $table->string('notify_email')->nullable(); // override admin email for notify_admin
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['trigger', 'is_active']);
        });

        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_rule_id')->nullable()->constrained('automation_rules')->nullOnDelete();
            $table->string('trigger');
            $table->string('summary')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index('trigger');
        });

        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->nullable()->constrained('chat_sessions')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('project_type');            // website|android|webapp|other
            $table->json('answers')->nullable();
            $table->decimal('estimated_min', 12, 2)->nullable();
            $table->decimal('estimated_max', 12, 2)->nullable();
            $table->string('currency', 8)->default('INR');
            $table->string('status')->default('new');   // new|reviewed|sent|won|lost
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
        Schema::dropIfExists('automation_logs');
        Schema::dropIfExists('automation_rules');
    }
};
