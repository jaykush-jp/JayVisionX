<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            if (! Schema::hasColumn('case_studies', 'category')) {
                $table->string('category')->nullable()->after('industry'); // type of work
            }
        });
    }

    public function down(): void
    {
        Schema::table('case_studies', function (Blueprint $table) {
            if (Schema::hasColumn('case_studies', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
