<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Clean up legacy/bad `stats` values on case_studies. The column is cast to an
 * array, but some rows stored a non-array string, which crashed the Our Work
 * pages (array_slice/foreach on a string). A valid stored value is a JSON array
 * ("[...]"); anything else is reset to NULL. New saves are sanitised by the
 * admin controller, so this only has to fix old data.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('case_studies') || ! Schema::hasColumn('case_studies', 'stats')) {
            return;
        }

        DB::table('case_studies')
            ->whereNotNull('stats')
            ->where('stats', 'not like', '[%')
            ->update(['stats' => null]);
    }

    public function down(): void
    {
        // Irreversible data cleanup — nothing to roll back.
    }
};
