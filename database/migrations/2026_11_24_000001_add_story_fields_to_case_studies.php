<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Richer "Featured Work Story" fields for the redesigned Our Work page:
 * an overview, key feature list, technology stack and a live project link.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            if (! Schema::hasColumn('case_studies', 'overview')) {
                $table->text('overview')->nullable()->after('category');
            }
            if (! Schema::hasColumn('case_studies', 'features')) {
                $table->json('features')->nullable()->after('result');
            }
            if (! Schema::hasColumn('case_studies', 'tech_stack')) {
                $table->json('tech_stack')->nullable()->after('features');
            }
            if (! Schema::hasColumn('case_studies', 'project_url')) {
                $table->string('project_url')->nullable()->after('tech_stack');
            }
        });
    }

    public function down(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            foreach (['overview', 'features', 'tech_stack', 'project_url'] as $col) {
                if (Schema::hasColumn('case_studies', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
