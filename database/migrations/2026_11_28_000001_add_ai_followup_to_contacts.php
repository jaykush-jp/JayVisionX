<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'ai_followups_sent')) {
                $table->unsignedSmallInteger('ai_followups_sent')->default(0)->after('follow_up_at');
            }
            if (! Schema::hasColumn('contacts', 'ai_followup_at')) {
                $table->timestamp('ai_followup_at')->nullable()->after('ai_followups_sent');
            }
            if (! Schema::hasColumn('contacts', 'ai_followup_opt_out')) {
                $table->boolean('ai_followup_opt_out')->default(false)->after('ai_followup_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            foreach (['ai_followups_sent', 'ai_followup_at', 'ai_followup_opt_out'] as $col) {
                if (Schema::hasColumn('contacts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
