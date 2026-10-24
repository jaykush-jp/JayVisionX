<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 — adds pricing/detail columns to services, CRM columns to contacts,
 * and a service link to faqs. All nullable/defaulted — existing rows are safe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('category')->nullable()->after('slug');
            $table->json('deliverables')->nullable()->after('features');
            $table->json('tech_stack')->nullable()->after('deliverables');
            $table->json('exclusions')->nullable()->after('tech_stack');
            $table->json('related_projects')->nullable()->after('exclusions'); // project ids
            $table->decimal('starting_price', 12, 2)->nullable()->after('related_projects');
            $table->string('price_type')->nullable()->after('starting_price'); // fixed, starting_from, hourly, monthly, custom
            $table->string('pricing_unit')->nullable()->after('price_type');
            $table->string('currency', 8)->default('INR')->after('pricing_unit');
            $table->string('estimated_delivery')->nullable()->after('currency');
        });

        // NOTE: status / source / value / last_contacted_at / follow_up_at already
        // exist from the crm_and_integrations migration — only AI/quote fields added here.
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('preferred_contact')->nullable()->after('follow_up_at');
            $table->string('project_type')->nullable()->after('preferred_contact');
            $table->text('project_description')->nullable()->after('project_type');
            $table->string('deadline')->nullable()->after('project_description');
            $table->string('campaign')->nullable()->after('deadline');
            $table->unsignedBigInteger('chat_session_id')->nullable()->after('campaign');
            $table->boolean('consent')->default(false)->after('chat_session_id');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'category', 'deliverables', 'tech_stack', 'exclusions', 'related_projects',
                'starting_price', 'price_type', 'pricing_unit', 'currency', 'estimated_delivery',
            ]);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_contact', 'project_type', 'project_description', 'deadline',
                'campaign', 'chat_session_id', 'consent',
            ]);
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn('service_id');
        });
    }
};
