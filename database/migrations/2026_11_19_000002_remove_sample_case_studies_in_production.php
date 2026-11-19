<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * On the LIVE site, Our Work should show only the real client work the admin
 * adds — not seeded sample data. This removes the previously-seeded sample case
 * studies in production only (local/staging keep them for design testing).
 */
return new class extends Migration
{
    private array $sampleSlugs = [
        '3x-qualified-leads-real-estate',
        'ecommerce-revenue-doubled',
        'dental-clinic-local-no1',
        'meta-ads-5x-roas-fashion',
        'app-downloads-tripled-aso',
        // older legacy samples
        '3x-leads-for-a-real-estate-builder',
        'e-commerce-revenue-doubled',
        '1-google-ranking-for-a-local-clinic',
    ];

    public function up(): void
    {
        if (! app()->environment('production') || ! Schema::hasTable('case_studies')) {
            return;
        }

        DB::table('case_studies')->whereIn('slug', $this->sampleSlugs)->delete();
    }

    public function down(): void
    {
        // Sample data removal is not reversible.
    }
};
