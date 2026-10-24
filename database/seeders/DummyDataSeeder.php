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
        $this->customers();
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
        if (EmailTemplate::count() > 0) {
            return;
        }

        $templates = [
            ['Welcome / First reply', 'Thanks for reaching out to JayVisionX', "Hi,\n\nThank you for contacting JayVisionX! We've received your enquiry and our team will get back to you shortly.\n\nIn the meantime, feel free to call or WhatsApp us for anything urgent.\n\nBest regards,\nTeam JayVisionX"],
            ['First Follow-up', 'Following up on your enquiry', "Hi,\n\nJust following up on your enquiry with JayVisionX. We'd love to understand your requirements and share how we can help you grow.\n\nWhen would be a good time for a quick call?\n\nBest regards,\nTeam JayVisionX"],
            ['Proposal Sent', 'Your proposal from JayVisionX', "Hi,\n\nAs discussed, please find our proposal attached. It includes the scope, timeline and pricing for your project.\n\nHappy to walk you through it on a call. Looking forward to working together!\n\nBest regards,\nTeam JayVisionX"],
            ['Quote Follow-up', 'Any questions on your quote?', "Hi,\n\nI wanted to check if you had any questions on the quotation we shared. We're happy to adjust the scope to match your budget and goals.\n\nBest regards,\nTeam JayVisionX"],
            ['Thank You / Won', 'Welcome aboard!', "Hi,\n\nThank you for choosing JayVisionX! We're excited to work with you. Our team will reach out with the next steps and project kickoff details.\n\nBest regards,\nTeam JayVisionX"],
        ];

        foreach ($templates as [$name, $subject, $body]) {
            EmailTemplate::create(compact('name', 'subject', 'body'));
        }
    }

    private function customers(): void
    {
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
