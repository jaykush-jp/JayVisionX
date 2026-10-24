<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\CaseStudyController;
use App\Http\Controllers\Frontend\ChatbotController;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\Frontend\LegalController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminCaseStudyController;
use App\Http\Controllers\Admin\AdminKnowledgeController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminEmailTemplateController;
use App\Http\Controllers\Admin\AdminIntegrationController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSocialPostController;
use App\Http\Controllers\Admin\AdminAiSettingController;
use App\Http\Controllers\Admin\AdminInboxController;
use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\AdminAutomationController;
use App\Http\Controllers\Admin\AdminQuoteController;
use App\Http\Controllers\Admin\AdminReportController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/our-work', [CaseStudyController::class, 'index'])->name('our-work');
Route::get('/our-work/{slug}', [CaseStudyController::class, 'show'])->name('our-work.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/landing', [LandingController::class, 'index'])->name('landing');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/newsletter', [ContactController::class, 'newsletter'])->name('newsletter');

// Legal pages
Route::get('/privacy-policy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [LegalController::class, 'terms'])->name('terms');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /admin',
        'Disallow: /admin/',
        'Allow: /',
        '',
        'Sitemap: ' . route('sitemap'),
    ];

    return response(implode("\n", $lines) . "\n", 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

// Support chatbot
Route::post('/chatbot', [ChatbotController::class, 'reply'])->name('chatbot.reply');
Route::post('/chatbot/lead', [ChatbotController::class, 'lead'])->name('chatbot.lead');

// Live lead webhooks (Meta Lead Ads + Google Ads lead forms)
Route::get('/webhooks/meta/leads', [WebhookController::class, 'metaVerify'])->name('webhooks.meta.verify');
Route::post('/webhooks/meta/leads', [WebhookController::class, 'metaLeads'])->name('webhooks.meta.leads');
Route::post('/webhooks/google/leads', [WebhookController::class, 'googleLeads'])->name('webhooks.google.leads');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Auth
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware('admin.auth')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Services
        Route::resource('services', AdminServiceController::class)->except('show');
        Route::post('services/{id}/toggle-status', [AdminServiceController::class, 'toggleStatus'])->name('services.toggle-status');

        // Projects
        Route::resource('projects', AdminProjectController::class)->except('show');
        Route::post('projects/{id}/toggle-status', [AdminProjectController::class, 'toggleStatus'])->name('projects.toggle-status');

        // Case Studies
        Route::resource('case-studies', AdminCaseStudyController::class)->except('show');

        // Blogs
        Route::resource('blogs', AdminBlogController::class)->except('show');
        Route::post('blogs/{id}/toggle-status', [AdminBlogController::class, 'toggleStatus'])->name('blogs.toggle-status');

        // Testimonials
        Route::resource('testimonials', AdminTestimonialController::class)->except('show');

        // FAQs
        Route::resource('faqs', AdminFaqController::class)->except('show');

        // AI Support — Knowledge Base & Pricing
        Route::resource('knowledge', AdminKnowledgeController::class)->except('show');
        Route::post('knowledge/{id}/status', [AdminKnowledgeController::class, 'setStatus'])->name('knowledge.status');
        Route::resource('packages', AdminPackageController::class)->except('show');

        // AI Support — Configuration
        Route::get('ai-settings', [AdminAiSettingController::class, 'index'])->name('ai-settings.index');
        Route::post('ai-settings', [AdminAiSettingController::class, 'update'])->name('ai-settings.update');

        // AI Support — Live Inbox
        Route::get('inbox', [AdminInboxController::class, 'index'])->name('inbox.index');
        Route::get('inbox/{id}', [AdminInboxController::class, 'show'])->name('inbox.show')->whereNumber('id');
        Route::post('inbox/{id}/status', [AdminInboxController::class, 'updateStatus'])->name('inbox.status');
        Route::post('inbox/{id}/toggle-human', [AdminInboxController::class, 'toggleHuman'])->name('inbox.toggle-human');
        Route::post('inbox/{id}/assign', [AdminInboxController::class, 'assign'])->name('inbox.assign');
        Route::post('inbox/{id}/note', [AdminInboxController::class, 'note'])->name('inbox.note');
        Route::post('inbox/{id}/convert', [AdminInboxController::class, 'convertToLead'])->name('inbox.convert');

        // AI Support — Audit logs
        Route::get('audit', [AdminAuditController::class, 'index'])->name('audit.index');

        // Automation rules
        Route::post('automation/{automation}/toggle', [AdminAutomationController::class, 'toggle'])->name('automation.toggle');
        Route::resource('automation', AdminAutomationController::class)->except('show');

        // Quote builder + requests
        Route::get('quotes/build', [AdminQuoteController::class, 'build'])->name('quotes.build');
        Route::get('quotes', [AdminQuoteController::class, 'index'])->name('quotes.index');
        Route::get('quotes/{quote}', [AdminQuoteController::class, 'show'])->name('quotes.show')->whereNumber('quote');
        Route::post('quotes/{quote}/status', [AdminQuoteController::class, 'updateStatus'])->name('quotes.status');

        // Reports & analytics
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');

        // Leads / Customer CRM (backed by the contacts table)
        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/export', [AdminContactController::class, 'export'])->name('contacts.export');
        Route::get('customers', [AdminContactController::class, 'index'])->name('customers');
        Route::post('contacts', [AdminContactController::class, 'store'])->name('contacts.store');
        Route::get('contacts/{id}', [AdminContactController::class, 'show'])->name('contacts.show')->whereNumber('id');
        Route::delete('contacts/{id}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
        Route::post('contacts/{id}/mark-read', [AdminContactController::class, 'markRead'])->name('contacts.mark-read');
        Route::post('contacts/{id}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.status');
        Route::post('contacts/{id}/note', [AdminContactController::class, 'storeNote'])->name('contacts.note');
        Route::post('contacts/{id}/follow-up', [AdminContactController::class, 'setFollowUp'])->name('contacts.follow-up');
        Route::post('contacts/{id}/reply', [AdminContactController::class, 'reply'])->name('contacts.reply');

        // Email templates
        Route::resource('email-templates', AdminEmailTemplateController::class)->except('show');

        // Marketing — integrations, reviews, social posts
        Route::get('integrations', [AdminIntegrationController::class, 'index'])->name('integrations.index');
        Route::get('integrations/{provider}/connect', [AdminIntegrationController::class, 'connect'])->name('integrations.connect');
        Route::get('integrations/{provider}/callback', [AdminIntegrationController::class, 'callback'])->name('integrations.callback');
        Route::post('integrations/{provider}/disconnect', [AdminIntegrationController::class, 'disconnect'])->name('integrations.disconnect');

        Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('reviews/{reviewId}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');

        Route::get('posts', [AdminSocialPostController::class, 'index'])->name('posts.index');
        Route::post('posts', [AdminSocialPostController::class, 'store'])->name('posts.store');
        Route::post('posts/{id}/publish', [AdminSocialPostController::class, 'publishNow'])->name('posts.publish');
        Route::delete('posts/{id}', [AdminSocialPostController::class, 'destroy'])->name('posts.destroy');

        // Settings
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // Pages (Home, About SEO)
        Route::resource('pages', AdminPageController::class)->except('show');
    });
});
