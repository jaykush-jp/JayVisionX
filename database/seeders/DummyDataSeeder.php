<?php

namespace Database\Seeders;

use App\Models\AutomationRule;
use App\Models\Contact;
use App\Models\EmailTemplate;
use App\Models\KnowledgeArticle;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Sample content for the admin modules so the AI assistant has real data to
 * ground on and the CRM/automation screens are populated for demos.
 *
 * Safe to run repeatedly — each section only seeds when its table is empty.
 */
class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->knowledge();
        $this->packages();
        $this->automation();
        $this->emailTemplates();
        $this->team();
        $this->customers();
        $this->projects();
        $this->caseStudies();
    }

    private function projects(): void
    {
        if (\App\Models\Project::count() > 0) {
            return;
        }

        $rows = [
            ['Shivaay Real Estate Website', 'Shivaay Group', 'Website', ['Laravel', 'MySQL', 'Tailwind'], 'A fast, SEO-optimised real-estate website with property listings and enquiry capture.'],
            ['MediCare Clinic App', 'MediCare', 'App', ['Flutter', 'Firebase'], 'Android app for appointment booking, reminders and patient records.'],
            ['TrendKart E-commerce', 'TrendKart', 'Website', ['Laravel', 'Razorpay', 'Vue'], 'Full e-commerce store with payments, inventory and an admin dashboard.'],
            ['EduLearn LMS', 'EduLearn', 'Web App', ['Laravel', 'Livewire'], 'Learning-management web app with courses, quizzes and progress tracking.'],
            ['FitLife Gym Branding', 'FitLife', 'Branding', ['Illustrator', 'Photoshop'], 'Complete brand identity, logo and social creatives for a fitness chain.'],
            ['LocalEats Food Delivery', 'LocalEats', 'App', ['React Native', 'Node.js'], 'Food-delivery app with live tracking, payments and a restaurant panel.'],
        ];

        foreach ($rows as $i => [$title, $client, $category, $tech, $desc]) {
            \App\Models\Project::create([
                'title'             => $title,
                'slug'              => Str::slug($title),
                'client_name'       => $client,
                'category'          => $category,
                'short_description' => $desc,
                'description'       => '<p>' . $desc . ' Delivered by JayVisionX with a focus on performance, design and measurable results.</p>',
                'technologies'      => $tech,
                'completion_date'   => now()->subMonths($i + 1)->toDateString(),
                'is_active'         => true,
                'show_on_home'      => $i < 4,
                'sort_order'        => $i + 1,
            ]);
        }
    }

    private function caseStudies(): void
    {
        // Remove the earlier seeded case studies that reused Project clients
        // (now replaced by the distinct, results-focused set below).
        \App\Models\CaseStudy::whereIn('slug', [
            '3x-leads-for-a-real-estate-builder',
            'e-commerce-revenue-doubled',
            '1-google-ranking-for-a-local-clinic',
        ])->delete();

        // Results-focused case studies with their OWN clients, kept distinct
        // from the build-portfolio in Projects. updateOrCreate by slug so this
        // refreshes the content on re-seed instead of duplicating.
        $rows = [
            ['3x-qualified-leads-real-estate', '3x Qualified Leads for a Real Estate Builder', 'Sunrise Realty', 'Real Estate',
                'The builder was spending on ads but getting mostly junk enquiries and a high cost per lead.',
                'We rebuilt the landing pages, restructured the Google & Meta Ads with better targeting, and added WhatsApp lead capture + instant follow-up.',
                '3x more qualified site visits booked in 90 days at a 42% lower cost per lead.',
                [['value' => '3x', 'label' => 'Qualified leads'], ['value' => '-42%', 'label' => 'Cost per lead'], ['value' => '90', 'label' => 'Days']]],
            ['ecommerce-revenue-doubled', 'E-commerce Revenue Doubled in 6 Months', 'Bloom Boutique', 'D2C / Retail',
                'Good traffic but a leaky checkout, abandoned carts and no retargeting.',
                'We fixed the store UX and checkout, set up abandoned-cart flows and launched Meta retargeting with fresh creatives.',
                'Conversion rate up 2.1x and online revenue doubled within 6 months.',
                [['value' => '2x', 'label' => 'Revenue'], ['value' => '+110%', 'label' => 'Conversions'], ['value' => '-31%', 'label' => 'Cart drop-off']]],
            ['dental-clinic-local-no1', '#1 Local Ranking for a Dental Clinic', 'SmileCare Dental', 'Healthcare',
                'The clinic was invisible on Google for nearby searches and relied only on referrals.',
                'Local SEO, Google Business Profile optimisation, review generation and consistent local content.',
                'Ranked #1 for 20+ local keywords and a 5x increase in appointment calls.',
                [['value' => '#1', 'label' => 'Local ranking'], ['value' => '20+', 'label' => 'Keywords'], ['value' => '5x', 'label' => 'Calls']]],
            ['meta-ads-5x-roas-fashion', '5x ROAS from Meta Ads for a Fashion Brand', 'UrbanThread', 'Fashion / D2C',
                'Ad spend was rising but return on ad spend (ROAS) kept falling and creatives felt stale.',
                'We rebuilt the funnel, tested scroll-stopping reels/creatives and tightened audience + retargeting layers.',
                'Scaled to a steady 5x ROAS while increasing monthly ad spend 3x profitably.',
                [['value' => '5x', 'label' => 'ROAS'], ['value' => '3x', 'label' => 'Ad spend scaled'], ['value' => '+180%', 'label' => 'Purchases']]],
            ['app-downloads-tripled-aso', 'App Downloads Tripled with ASO + Ads', 'FitPulse', 'Fitness',
                'A good app with very few organic installs and expensive paid downloads.',
                'App Store Optimisation (title, keywords, screenshots) plus targeted install campaigns and review prompts.',
                'Organic installs tripled and cost per install dropped by nearly half.',
                [['value' => '3x', 'label' => 'Organic installs'], ['value' => '-46%', 'label' => 'Cost per install'], ['value' => '4.6★', 'label' => 'Store rating']]],
        ];

        foreach ($rows as $i => [$slug, $title, $client, $industry, $challenge, $solution, $result, $stats]) {
            \App\Models\CaseStudy::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'      => $title,
                    'client'     => $client,
                    'industry'   => $industry,
                    'challenge'  => $challenge,
                    'solution'   => $solution,
                    'result'     => $result,
                    'content'    => '<p>' . $solution . ' ' . $result . '</p>',
                    'stats'      => $stats,
                    'is_active'  => true,
                    'sort_order' => $i + 1,
                ]
            );
        }
    }

    private function knowledge(): void
    {
        if (KnowledgeArticle::count() > 0) {
            return;
        }

        $items = [
            ['company_profile', 'About JayVisionX', "JayVisionX is a Digital Growth & Technology Company based in Delhi NCR, India. We help businesses grow with websites, mobile apps, custom software, SEO, Google & Meta Ads, and social media marketing. We have delivered 40+ projects for 50+ happy clients across India."],
            ['services', 'Services we offer', "We offer: Website Design & Development, Android & iOS App Development, Custom Software & Web Apps, SEO Services, Google Ads (PPC), Meta Ads (Facebook & Instagram), Social Media Marketing, Brand Identity & Graphic Design, Video & Reel Editing, Business Photoshoots and 360° Virtual Tours."],
            ['pricing', 'How our pricing works', "Pricing depends on your requirements. Websites typically start from a custom quote based on pages and features. We offer Basic, Standard and Premium packages for most services. Share your requirement for a tailored quotation — there are no hidden charges, and GST is applicable as per government rules."],
            ['process', 'Our work process', "1) Free consultation to understand your goals. 2) Proposal and quotation. 3) Design and development with regular updates. 4) Review and revisions. 5) Launch and handover. 6) Ongoing support and maintenance."],
            ['faqs', 'How long does a website take?', "A standard business website takes about 2–4 weeks depending on pages, content and features. E-commerce and web apps take longer. We share a clear timeline in the proposal before starting."],
            ['faqs', 'Do you provide support after launch?', "Yes. All projects include a support period after launch, and we offer monthly maintenance plans for updates, security and backups."],
            ['policies', 'Refund & revision policy', "We include a set number of revision rounds in every package. Advance payments are adjusted against the final invoice. Refunds are handled case-by-case per the signed proposal."],
            ['portfolio', 'Our portfolio & case studies', "We have delivered websites, mobile apps and marketing campaigns across real estate, healthcare, education, retail and local services. Ask us for case studies relevant to your industry."],
            ['contact', 'How to reach us', "Call or WhatsApp us, email info@jayvisionx.com, or fill the enquiry form on the website. We are based in Delhi NCR and reply within 24 hours on business days."],
        ];

        foreach ($items as $i => [$cat, $title, $content]) {
            KnowledgeArticle::create([
                'title'         => $title,
                'slug'          => Str::slug($title) . '-' . Str::random(5),
                'category'      => $cat,
                'content'       => $content,
                'status'        => 'published',
                'is_searchable' => true,
                'sort_order'    => $i,
                'last_updated_by' => 'seeder',
            ]);
        }
    }

    private function packages(): void
    {
        if (Package::count() > 0) {
            return;
        }

        // [service_id, name, tier, price_type, amount, timeline, features, sort]
        $rows = [
            // Website (service 2)
            [2, 'Starter Website', 'basic', 'starting_from', 14999, '1–2 weeks', ['Up to 5 pages', 'Mobile responsive', 'Contact form', 'Basic SEO setup'], 1],
            [2, 'Business Website', 'standard', 'starting_from', 29999, '2–3 weeks', ['Up to 12 pages', 'CMS / admin panel', 'Blog', 'On-page SEO', 'WhatsApp integration'], 2],
            [2, 'Premium / E-commerce', 'premium', 'starting_from', 59999, '3–6 weeks', ['Unlimited pages', 'E-commerce / payments', 'Advanced SEO', 'Analytics', '3 months support'], 3],
            // App Development (service 1)
            [1, 'Android App — MVP', 'standard', 'starting_from', 79999, '4–8 weeks', ['Android app', 'Backend API', 'Admin panel', 'Play Store deployment'], 1],
            // SEO (service 7)
            [7, 'SEO Growth', 'standard', 'monthly', 14999, 'Monthly', ['On-page + technical SEO', 'Keyword research', '8 blogs/month', 'Monthly report'], 1],
            [7, 'SEO Pro', 'premium', 'monthly', 24999, 'Monthly', ['Everything in Growth', 'Link building', 'Local SEO', 'Dedicated manager'], 2],
            // Social Media (service 5)
            [5, 'Social Media Basic', 'basic', 'monthly', 9999, 'Monthly', ['2 platforms', '12 posts/month', 'Basic design', 'Monthly report'], 1],
            // Google Ads (service 8)
            [8, 'Google Ads Management', 'standard', 'monthly', 11999, 'Monthly', ['Campaign setup', 'Keyword & bid management', 'Conversion tracking', 'Ad budget separate'], 1],
            // Web App (service 15) — custom
            [15, 'Web Application', 'premium', 'custom', null, '6–12 weeks', ['Custom modules', 'User roles', 'Dashboard & reports', 'API integrations'], 1],
        ];

        foreach ($rows as [$sid, $name, $tier, $type, $amount, $timeline, $features, $sort]) {
            Package::create([
                'service_id'         => $sid,
                'name'               => $name,
                'tier'               => $tier,
                'description'        => $name . ' package by JayVisionX.',
                'price_type'         => $type,
                'amount'             => $amount,
                'currency'           => 'INR',
                'estimated_timeline' => $timeline,
                'features'           => $features,
                'add_ons'            => ['Logo design', 'Content writing', 'Extra revisions'],
                'exclusions'         => ['Third-party paid tools', 'Ad spend budget'],
                'tax_note'           => 'GST 18% extra',
                'quote_validity'     => 'Valid for 15 days',
                'effective_date'     => now()->toDateString(),
                'is_active'          => true,
                'sort_order'         => $sort,
            ]);
        }
    }

    private function automation(): void
    {
        if (AutomationRule::count() > 0) {
            return;
        }

        $rules = [
            ['Notify team on new chat lead', 'lead_created', 'notify_admin', 'A new lead came in from the website chatbot.'],
            ['Alert on human handover', 'handover_requested', 'notify_admin', 'A visitor requested to talk to a human — please join the chat.'],
            ['Notify on quote request', 'quote_requested', 'notify_admin', 'A visitor requested a custom quote.'],
            ['Greet with an offer', 'chat_started', 'auto_message', 'By the way — we offer a free consultation. Ask me for a quote anytime!'],
            ['Flag unanswered chats', 'low_confidence', 'set_pending', null],
        ];

        foreach ($rules as $i => [$name, $trigger, $action, $msg]) {
            AutomationRule::create([
                'name'       => $name,
                'trigger'    => $trigger,
                'action'     => $action,
                'message'    => $msg,
                'is_active'  => true,
                'sort_order' => $i,
            ]);
        }
    }

    private function emailTemplates(): void
    {
        // Idempotent by name — adds missing templates without duplicating.
        $templates = [
            ['Welcome / First reply', 'Thanks for reaching out to JayVisionX', "Hi {name},\n\nThank you for contacting JayVisionX! We've received your enquiry and our team will get back to you shortly.\n\nIn the meantime, feel free to call or WhatsApp us for anything urgent.\n\nBest regards,\nTeam JayVisionX"],
            ['First Follow-up', 'Following up on your enquiry', "Hi {name},\n\nJust following up on your enquiry with JayVisionX. We'd love to understand your requirements and share how we can help you grow.\n\nWhen would be a good time for a quick call?\n\nBest regards,\nTeam JayVisionX"],
            ['Quotation / Estimate', 'Your quotation from JayVisionX', "Hi {name},\n\nThank you for your interest. Please find your quotation below/attached — it covers the scope, timeline and pricing for {service}.\n\nThis estimate is valid for 15 days. Happy to tailor the scope to your budget on a quick call.\n\nBest regards,\nTeam JayVisionX"],
            ['Proposal Sent', 'Your proposal from JayVisionX', "Hi {name},\n\nAs discussed, please find our proposal attached. It includes the scope, timeline, deliverables and pricing for your project.\n\nHappy to walk you through it on a call. Looking forward to working together!\n\nBest regards,\nTeam JayVisionX"],
            ['Quote Follow-up', 'Any questions on your quote?', "Hi {name},\n\nI wanted to check if you had any questions on the quotation we shared. We're happy to adjust the scope to match your budget and goals.\n\nBest regards,\nTeam JayVisionX"],
            ['Payment Reminder', 'Gentle reminder: invoice pending', "Hi {name},\n\nThis is a friendly reminder that invoice {invoice_no} for {amount} is due. You can pay via the link/details shared earlier.\n\nIf you've already paid, please ignore this message. Thank you!\n\nBest regards,\nTeam JayVisionX"],
            ['Project Kickoff', 'Welcome aboard — let’s get started 🚀', "Hi {name},\n\nThank you for choosing JayVisionX! We're excited to start on {project}. Here are the next steps:\n\n1. Share the required access/content\n2. We'll set up the project plan & timeline\n3. Weekly updates from your dedicated manager\n\nLet's build something great together!\n\nBest regards,\nTeam JayVisionX"],
            ['Project Delivered / Handover', 'Your project is live 🎉', "Hi {name},\n\nGreat news — {project} is complete and live! We've shared all the files, logins and documentation.\n\nYour support period starts today. Reach out anytime for updates or maintenance.\n\nThank you for trusting JayVisionX!\n\nBest regards,\nTeam JayVisionX"],
            ['Monthly Report', 'Your monthly performance report', "Hi {name},\n\nPlease find your monthly report for {month}. Highlights:\n\n• Traffic / leads / rankings summary\n• Campaign performance & spend\n• What we'll focus on next month\n\nHappy to discuss on a call. Thanks for growing with us!\n\nBest regards,\nTeam JayVisionX"],
            ['Re-engagement', 'Still thinking it over?', "Hi {name},\n\nWe noticed we didn't hear back on your enquiry. No worries — we'd still love to help you grow.\n\nWould a quick 10-minute call this week work? Or reply with your questions and we'll guide you.\n\nBest regards,\nTeam JayVisionX"],
            ['Review Request', 'Mind sharing a quick review? ⭐', "Hi {name},\n\nWe hope you're happy with the results from JayVisionX! A short Google review would mean a lot and help other businesses find us.\n\nHere's the link: {review_link}\n\nThank you so much!\n\nBest regards,\nTeam JayVisionX"],
            ['Thank You / Won', 'Welcome aboard!', "Hi {name},\n\nThank you for choosing JayVisionX! We're excited to work with you. Our team will reach out with the next steps and project kickoff details.\n\nBest regards,\nTeam JayVisionX"],
        ];

        foreach ($templates as [$name, $subject, $body]) {
            EmailTemplate::updateOrCreate(['name' => $name], compact('subject', 'body'));
        }
    }

    private function team(): void
    {
        if (\App\Models\TeamMember::count() > 0) {
            return;
        }

        $members = [
            ['Jay Kushwah', 'Founder & CEO', 'Leads strategy, growth and client success at JayVisionX.'],
            ['Digital Marketing Lead', 'Head of Marketing', 'Owns SEO, Google & Meta Ads and paid growth.'],
            ['Design Lead', 'Creative Head', 'Brand identity, UI/UX and all creative design.'],
            ['Development Lead', 'Tech Head', 'Websites, web apps and mobile app development.'],
            ['Social Media Manager', 'Social & Content', 'Content, reels and social media campaigns.'],
            ['Account Manager', 'Client Success', 'Your single point of contact for everything.'],
        ];

        foreach ($members as $i => [$name, $designation, $role]) {
            \App\Models\TeamMember::create([
                'name'        => $name,
                'designation' => $designation,
                'role'        => $role,
                'is_active'   => true,
                'sort_order'  => $i + 1,
            ]);
        }
    }

    private function customers(): void
    {
        // Demo CRM leads are for local/staging only — never seed fake leads into production.
        if (app()->environment('production')) {
            return;
        }

        if (Contact::where('source', '!=', 'contact_form')->whereIn('status', ['won'])->count() > 0) {
            return;
        }

        // Mix of statuses + sources (incl. meta & google so live-lead sources show).
        $leads = [
            ['Amit Verma', 'amit.verma@example.com', '9810012345', 'Website Design & Development', 'won',       'contact_form', 45000],
            ['Neha Gupta', 'neha.gupta@example.com', '9820023456', 'SEO Services', 'won',                       'google',       15000],
            ['Rahul Mehta', 'rahul.mehta@example.com', '9830034567', 'Meta Ads Management', 'won',              'meta',         12000],
            ['Pooja Sharma', 'pooja.sharma@example.com', '9840045678', 'App Development', 'qualified',          'chatbot',      80000],
            ['Vikram Singh', 'vikram.singh@example.com', '9850056789', 'Social Media Marketing', 'proposal',    'meta',         10000],
            ['Sana Khan', 'sana.khan@example.com', '9860067890', 'Google Ads Management', 'contacted',          'google',       12000],
            ['Deepak Rao', 'deepak.rao@example.com', '9870078901', 'Web App Development', 'new',                'landing',      null],
            ['Ritu Jain', 'ritu.jain@example.com', '9880089012', 'Brand Identity & Design', 'won',             'contact_form', 25000],
        ];

        foreach ($leads as [$name, $email, $phone, $service, $status, $source, $value]) {
            Contact::create([
                'name'               => $name,
                'email'              => $email,
                'phone'              => $phone,
                'service_interested' => $service,
                'budget'             => $value ? '₹' . number_format($value) : 'To be discussed',
                'message'            => 'Interested in ' . $service . '. Please share details and pricing.',
                'status'             => $status,
                'source'             => $source,
                'value'              => $value,
                'is_read'            => $status !== 'new',
                'last_contacted_at'  => in_array($status, ['new']) ? null : now()->subDays(rand(1, 20)),
            ]);
        }
    }
}
