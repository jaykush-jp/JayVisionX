<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Turns the flat "contacts" enquiry list into a workable Lead/Customer CRM and
 * adds the storage the Integrations hub (OAuth tokens) and social composer need.
 */
return new class extends Migration
{
    public function up(): void
    {
        // --- Extend contacts into leads ---------------------------------------
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('status')->default('new')->after('is_read');     // new|contacted|qualified|proposal|won|lost
            $table->string('source')->nullable()->after('status');          // contact_form|landing|chatbot|manual
            $table->decimal('value', 12, 2)->nullable()->after('source');   // deal value
            $table->timestamp('last_contacted_at')->nullable()->after('value');
            $table->timestamp('follow_up_at')->nullable()->after('last_contacted_at');

            $table->index('status');
            $table->index('follow_up_at');
        });

        // --- Lead activity timeline (notes, calls, status changes, emails) ----
        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('type')->default('note');   // note|call|email|status_change
            $table->text('body');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index('contact_id');
        });

        // --- Log of emails sent to a lead from the admin ----------------------
        Schema::create('lead_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('subject');
            $table->longText('body');
            $table->string('sent_by')->nullable();
            $table->string('status')->default('sent'); // sent|failed
            $table->timestamps();

            $table->index('contact_id');
        });

        // --- Reusable email templates for replies -----------------------------
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
        });

        // --- OAuth tokens for external integrations (Google / Meta) -----------
        Schema::create('oauth_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('provider');                 // google|meta
            $table->string('account_label')->nullable();
            $table->longText('access_token')->nullable();   // encrypted at the model
            $table->longText('refresh_token')->nullable();  // encrypted at the model
            $table->timestamp('expires_at')->nullable();
            $table->text('scopes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique('provider');
        });

        // --- Social posts (Google Business Profile / Facebook / Instagram) ----
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->longText('body');
            $table->string('image')->nullable();
            $table->json('platforms')->nullable();      // ["google","facebook","instagram"]
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('draft'); // draft|scheduled|published|failed
            $table->json('external_ids')->nullable();   // provider => remote post id
            $table->text('error')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
        Schema::dropIfExists('oauth_tokens');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('lead_emails');
        Schema::dropIfExists('lead_notes');

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['follow_up_at']);
            $table->dropColumn(['status', 'source', 'value', 'last_contacted_at', 'follow_up_at']);
        });
    }
};
