<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds composite indexes on the columns the app filters/sorts by on every
 * request (is_active + sort_order, show_on_home, is_read). Non-destructive.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'services_active_order_idx');
            $table->index('show_on_home', 'services_show_on_home_idx');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'projects_active_order_idx');
            $table->index('show_on_home', 'projects_show_on_home_idx');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'blogs_active_order_idx');
        });

        Schema::table('case_studies', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'case_studies_active_order_idx');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'faqs_active_order_idx');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'testimonials_active_order_idx');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->index('is_read', 'contacts_is_read_idx');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_active_order_idx');
            $table->dropIndex('services_show_on_home_idx');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_active_order_idx');
            $table->dropIndex('projects_show_on_home_idx');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex('blogs_active_order_idx');
        });

        Schema::table('case_studies', function (Blueprint $table) {
            $table->dropIndex('case_studies_active_order_idx');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex('faqs_active_order_idx');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropIndex('testimonials_active_order_idx');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('contacts_is_read_idx');
        });
    }
};
