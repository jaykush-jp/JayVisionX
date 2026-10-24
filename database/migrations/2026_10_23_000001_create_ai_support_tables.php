<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 — data foundation for the AI Support & Automation system.
 * Additive only: creates new tables, touches no existing data.
 */
return new class extends Migration
{
    public function up(): void
    {
        // AI configuration (single row, admin-managed; the API key stays in .env).
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('anthropic');
            $table->string('model')->default('claude-haiku-5-5');
            $table->text('business_description')->nullable();
            $table->longText('system_prompt')->nullable();
            $table->text('welcome_message')->nullable();
            $table->json('suggested_questions')->nullable();
            $table->json('languages')->nullable();           // ["en","hi","hinglish"]
            $table->string('response_style')->default('friendly');
            $table->integer('max_response_tokens')->default(1024);
            $table->decimal('confidence_threshold', 3, 2)->nullable();
            $table->text('fallback_message')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('rate_limit_per_minute')->default(10);
            $table->integer('daily_message_limit')->nullable();
            $table->decimal('monthly_cost_cap', 10, 2)->nullable();
            $table->timestamps();
        });

        // Knowledge base — only "published" entries may be used by the public chatbot.
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('category')->default('faqs'); // company_profile, services, pricing, faqs, portfolio, policies, process, contact
            $table->longText('content')->nullable();
            $table->string('source_url')->nullable();
            $table->string('status')->default('draft');  // draft, published, archived
            $table->boolean('is_searchable')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('last_updated_by')->nullable();
            $table->timestamps();

            $table->index(['status', 'category'], 'knowledge_status_category_idx');
        });

        // Pricing packages (Basic / Standard / Premium / custom), linked to a service.
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->nullable()->index();
            $table->string('name');
            $table->string('tier')->nullable();          // basic, standard, premium, custom
            $table->text('description')->nullable();
            $table->string('price_type')->default('starting_from'); // fixed, starting_from, hourly, monthly, custom
            $table->decimal('amount', 12, 2)->nullable(); // null => custom quote
            $table->string('currency', 8)->default('INR');
            $table->string('pricing_unit')->nullable();   // per_project, per_hour, per_month
            $table->decimal('min_budget', 12, 2)->nullable();
            $table->decimal('max_budget', 12, 2)->nullable();
            $table->string('estimated_timeline')->nullable();
            $table->json('features')->nullable();
            $table->json('add_ons')->nullable();
            $table->json('exclusions')->nullable();
            $table->string('tax_note')->nullable();       // e.g. "+18% GST"
            $table->string('quote_validity')->nullable(); // e.g. "Valid for 15 days"
            $table->date('effective_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order'], 'packages_active_order_idx');
        });

        // Persisted chat conversations.
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_token')->unique();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->string('visitor_phone')->nullable();
            $table->string('status')->default('open');    // open, pending, resolved, closed
            $table->boolean('is_ai_active')->default(true); // false after human takeover
            $table->string('assigned_to')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable()->index(); // contacts.id
            $table->string('source_page')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('consent')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'last_message_at'], 'chat_sessions_status_idx');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_session_id')->index();
            $table->string('role')->default('user');      // user, assistant, agent, system
            $table->longText('content');
            $table->json('sources')->nullable();          // knowledge/source references used
            $table->json('meta')->nullable();             // model, tokens, confidence, etc.
            $table->timestamps();

            $table->index(['chat_session_id', 'created_at'], 'chat_messages_session_idx');
        });

        // Audit trail for admin changes (prices, knowledge, AI settings...).
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');                     // created, updated, deleted, published, archived
            $table->string('entity_type');                // package, knowledge_article, ai_setting, service...
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('performed_by')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'created_at'], 'audit_logs_entity_idx');
        });

        // Seed a default AI settings row so the admin has something to edit.
        DB::table('ai_settings')->insert([
            'provider'            => 'anthropic',
            'model'               => 'claude-haiku-5-5',
            'business_description' => 'JayVisionX is a Digital Marketing & Website/App Development agency in Delhi NCR.',
            'welcome_message'     => "Hi! Welcome to JayVisionX 👋 I'm your AI assistant. I can help you explore our website development, Android apps, web applications, custom software, digital marketing and creative services. What are you looking to build or grow?",
            'suggested_questions' => json_encode([
                'Explore Services',
                'Website Development Pricing',
                'Android App Pricing',
                'View Portfolio',
                'Request a Custom Quote',
                'Talk to Human Support',
            ]),
            'languages'           => json_encode(['en', 'hi', 'hinglish']),
            'response_style'      => 'friendly',
            'max_response_tokens' => 1024,
            'fallback_message'    => "I'm not fully sure about that. Would you like me to connect you with our team? You can also share your details for a callback.",
            'is_enabled'          => true,
            'rate_limit_per_minute' => 10,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('ai_settings');
    }
};
