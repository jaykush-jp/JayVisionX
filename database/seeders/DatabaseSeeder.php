<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Blog;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedServices();
        $this->seedTestimonials();
        $this->seedFaqs();
        $this->seedPages();
        $this->seedBlogs();
    }

    private function seedSettings(): void
    {
        $settings = [
            'site_name'          => 'JayVisionX',
            'site_tagline'       => 'Digital Marketing Agency & Website Development Company',
            'site_description'   => 'JayVisionX offers SEO services, Google Ads, Meta Ads, social media marketing, website development, web app development, Android & iOS app development, graphic design, video editing, and complete digital growth solutions.',
            'site_email'         => 'info@jayvisionx.com',
            'site_phone'         => '+91-9999999999',
            'site_address'       => 'Delhi NCR, India',
            'site_logo'          => '',
            'site_favicon'       => '',
            'whatsapp_number'    => '919999999999',
            'facebook_url'       => 'https://facebook.com/jayvisionx',
            'instagram_url'      => 'https://instagram.com/jayvisionx',
            'twitter_url'        => 'https://twitter.com/jayvisionx',
            'linkedin_url'       => 'https://linkedin.com/company/jayvisionx',
            'youtube_url'        => 'https://youtube.com/@jayvisionx',
            'footer_about'       => 'JayVisionX is a leading Digital Marketing Agency & IT Company in Delhi NCR. We help businesses grow with powerful digital strategies, stunning websites, and result-driven marketing campaigns.',
            'hero_title'         => 'Grow Your Business with Website Development, SEO, Ads & Creative Digital Solutions',
            'hero_subtitle'      => 'We deliver measurable results through powerful Digital Marketing, SEO, PPC, Social Media Marketing, and cutting-edge Website & App Development.',
            'hero_cta_text'      => 'Get Free Consultation',
            'hero_cta_link'      => '/contact',
            'hero_bg_image'      => '',
            'google_analytics'   => '',
            'google_tag_manager' => '',
            'meta_title'         => 'JayVisionX - Digital Marketing Agency & Website Development Company in Delhi NCR',
            'meta_description'   => 'JayVisionX is a top Digital Marketing Agency in Delhi NCR offering SEO, Google Ads, Meta Ads, Social Media Marketing, Website Development, App Development & more.',
            'meta_keywords'      => 'Digital Marketing Agency Delhi NCR, Website Development Company Delhi, SEO Services Delhi, Google Ads Management, Social Media Marketing Agency, Laravel Website Development',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    private function seedServices(): void
    {
        $services = [
            ['title' => 'App Development', 'slug' => 'app-development', 'icon' => 'fa-mobile-alt', 'short_description' => 'Custom Android & iOS app development with Flutter and native technologies for powerful mobile experiences.', 'sort_order' => 1],
            ['title' => 'Website Design & Development', 'slug' => 'website-design-development', 'icon' => 'fa-laptop-code', 'short_description' => 'Stunning, responsive website design and development using Laravel, PHP, React.js and modern frameworks.', 'sort_order' => 2],
            ['title' => 'Custom Software Development', 'slug' => 'custom-software-development', 'icon' => 'fa-cogs', 'short_description' => 'Tailored software solutions built to solve your unique business challenges and automate operations.', 'sort_order' => 3],
            ['title' => 'Brand Identity & Design', 'slug' => 'brand-identity-design', 'icon' => 'fa-paint-brush', 'short_description' => 'Complete brand identity design including logo, brand kit, style guides and visual identity systems.', 'sort_order' => 4],
            ['title' => 'Social Media Marketing', 'slug' => 'social-media-marketing', 'icon' => 'fa-share-alt', 'short_description' => 'Strategic social media marketing campaigns on Instagram, Facebook, LinkedIn to grow your audience and generate leads.', 'sort_order' => 5],
            ['title' => '360° Photography & Virtual Tours', 'slug' => '360-photography-virtual-tours', 'icon' => 'fa-camera', 'short_description' => 'Immersive 360° photography and virtual tour experiences for real estate, hospitality, and retail businesses.', 'sort_order' => 6],
            ['title' => 'SEO Services in Delhi NCR', 'slug' => 'seo-services-delhi-ncr', 'icon' => 'fa-search', 'short_description' => 'Proven SEO strategies to rank your website on Google top positions and drive organic traffic in Delhi NCR.', 'sort_order' => 7],
            ['title' => 'PPC & Google Ads Management', 'slug' => 'ppc-google-ads-management', 'icon' => 'fa-ad', 'short_description' => 'ROI-focused Google Ads and PPC campaigns to generate quality leads and maximize your advertising budget.', 'sort_order' => 8],
            ['title' => 'Business Profile Management', 'slug' => 'business-profile-management', 'icon' => 'fa-building', 'short_description' => 'Google My Business optimization and profile management to dominate local search results.', 'sort_order' => 9],
            ['title' => 'Marketing Strategy & Consulting', 'slug' => 'marketing-strategy-consulting', 'icon' => 'fa-chart-line', 'short_description' => 'Data-driven marketing strategy and business consulting to create a roadmap for sustainable growth.', 'sort_order' => 10],
            ['title' => 'Meta Ads Management', 'slug' => 'meta-ads-management', 'icon' => 'fa-facebook', 'short_description' => 'Expert Facebook and Instagram Ads management to reach your target audience and convert leads.', 'sort_order' => 11],
            ['title' => 'Graphic Design & Creative Design', 'slug' => 'graphic-design-creative', 'icon' => 'fa-palette', 'short_description' => 'Eye-catching graphic design, social media creatives, banners, brochures and all marketing materials.', 'sort_order' => 12],
            ['title' => 'Video Editing & Reel Editing', 'slug' => 'video-editing-reel-editing', 'icon' => 'fa-video', 'short_description' => 'Professional video editing, reel editing, motion graphics and brand videos to captivate your audience.', 'sort_order' => 13],
            ['title' => 'Business Photoshoot', 'slug' => 'business-photoshoot', 'icon' => 'fa-camera-retro', 'short_description' => 'Professional business, product, and corporate photoshoot services to elevate your brand image.', 'sort_order' => 14],
            ['title' => 'Web App Development', 'slug' => 'web-app-development', 'icon' => 'fa-code', 'short_description' => 'Scalable web applications built with React.js, Node.js, Laravel and Vue.js for complex business processes.', 'sort_order' => 15],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                array_merge($service, ['is_active' => true, 'show_on_home' => true])
            );
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['client_name' => 'Rajesh Kumar', 'company' => 'TechStart Solutions', 'designation' => 'CEO', 'review' => 'JayVisionX transformed our online presence completely. Our website traffic increased by 300% and we are getting quality leads every day. Their SEO and Google Ads team is simply outstanding!', 'rating' => 5],
            ['client_name' => 'Priya Sharma', 'company' => 'Fashion Boutique Delhi', 'designation' => 'Founder', 'review' => 'The social media marketing and Instagram Ads campaigns by JayVisionX helped us grow our sales by 5x. Their creative team is incredibly talented and understands our brand voice perfectly.', 'rating' => 5],
            ['client_name' => 'Amit Verma', 'company' => 'Real Estate Empire', 'designation' => 'Director', 'review' => 'Exceptional website development and digital marketing services. They built a beautiful, fast website and their SEO work got us to the first page of Google for all our target keywords in Delhi NCR.', 'rating' => 5],
            ['client_name' => 'Sunita Jain', 'company' => 'Healthcare Plus', 'designation' => 'Managing Partner', 'review' => 'The app development team built our Android and iOS app within 3 months. It is clean, fast, and our patients love using it. JayVisionX is our go-to tech partner for everything digital.', 'rating' => 5],
        ];

        foreach ($testimonials as $i => $t) {
            Testimonial::updateOrCreate(
                ['client_name' => $t['client_name']],
                array_merge($t, ['is_active' => true, 'sort_order' => $i + 1])
            );
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            ['question' => 'What digital marketing services do you offer in Delhi NCR?', 'answer' => 'JayVisionX offers a complete range of digital marketing services in Delhi NCR including SEO, Google Ads (PPC), Meta Ads (Facebook & Instagram), Social Media Marketing, Content Marketing, Email Marketing, and complete Digital Growth Strategy.'],
            ['question' => 'How long does it take to build a website?', 'answer' => 'A standard business website takes 2-4 weeks. A custom web application takes 4-12 weeks depending on complexity. An e-commerce website takes 3-6 weeks. We provide a detailed timeline before starting the project.'],
            ['question' => 'Do you develop Android and iOS apps?', 'answer' => 'Yes! We develop native Android apps (Java/Kotlin), native iOS apps (Swift), and cross-platform apps using Flutter. We handle everything from UI/UX design to development, testing, and Play Store/App Store publishing.'],
            ['question' => 'How much do your SEO services cost?', 'answer' => 'Our SEO packages start from ₹8,000/month for local SEO in Delhi NCR. The cost depends on the number of keywords, competition level, and website size. We offer customized packages based on your goals and budget.'],
            ['question' => 'Do you provide Google My Business optimization?', 'answer' => 'Yes, we provide complete Google My Business (Google Business Profile) optimization, setup, regular posting, Q&A management, review management, and local SEO strategies to help you rank in the local 3-pack.'],
            ['question' => 'Can you redesign my existing website?', 'answer' => 'Absolutely! We specialize in website redesigning with a focus on improved user experience, faster loading speed, better SEO structure, and conversion rate optimization. We ensure your new website looks modern and performs better.'],
            ['question' => 'What is your Meta Ads management process?', 'answer' => 'We start with understanding your business goals and target audience, then create compelling ad creatives, set up proper pixel tracking, launch campaigns, monitor performance daily, and optimize for best ROI. We provide weekly/monthly reports.'],
            ['question' => 'Do you offer website maintenance services?', 'answer' => 'Yes, we offer comprehensive website maintenance packages including regular updates, security monitoring, performance optimization, content updates, backup management, and technical support at affordable monthly rates.'],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['is_active' => true, 'sort_order' => $i + 1])
            );
        }
    }

    private function seedPages(): void
    {
        $pages = [
            ['page_name' => 'home', 'meta_title' => 'JayVisionX - Digital Marketing Agency & Website Development Company in Delhi NCR', 'meta_description' => 'JayVisionX is a leading Digital Marketing Agency in Delhi NCR. We offer SEO, Google Ads, Meta Ads, Social Media Marketing, Website Development, App Development & more. Get Free Consultation!', 'meta_keywords' => 'Digital Marketing Agency Delhi NCR, Website Development Company, SEO Services Delhi, Google Ads Management'],
            ['page_name' => 'about', 'title' => 'About JayVisionX', 'meta_title' => 'About Us - JayVisionX Digital Marketing Agency Delhi NCR', 'meta_description' => 'Learn about JayVisionX - A trusted Digital Marketing Agency & IT Company in Delhi NCR. We help businesses grow with SEO, Ads, and powerful digital solutions.'],
            ['page_name' => 'services', 'title' => 'Our Services', 'meta_title' => 'Digital Marketing & Web Development Services - JayVisionX', 'meta_description' => 'Explore JayVisionX services: SEO, Google Ads, Meta Ads, Social Media, Website Development, App Development, Graphic Design, Video Editing and more.'],
            ['page_name' => 'projects', 'title' => 'Our Projects & Portfolio', 'meta_title' => 'Projects & Portfolio - JayVisionX Digital Agency', 'meta_description' => 'Explore our successful projects in website development, app development, digital marketing, and creative design.'],
            ['page_name' => 'blog', 'title' => 'Digital Marketing Blog', 'meta_title' => 'Blog - Digital Marketing Tips & Insights | JayVisionX', 'meta_description' => 'Read our latest blog posts about digital marketing, SEO, Google Ads, social media marketing, website development, and business growth strategies.'],
            ['page_name' => 'contact', 'title' => 'Contact Us', 'meta_title' => 'Contact JayVisionX - Get Free Digital Marketing Consultation', 'meta_description' => 'Contact JayVisionX for SEO, Google Ads, website development, and digital marketing services in Delhi NCR. Get a free consultation today!'],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['page_name' => $page['page_name']], $page);
        }
    }

    private function seedBlogs(): void
    {
        $blogs = [
            [
                'title'    => 'Top 10 SEO Strategies to Rank Your Business in Delhi NCR in 2024',
                'slug'     => 'top-seo-strategies-delhi-ncr-2024',
                'author'   => 'JayVisionX Team',
                'category' => 'SEO',
                'excerpt'  => 'Discover the most effective SEO strategies to get your Delhi NCR business ranking on the first page of Google and drive quality organic traffic.',
                'content'  => '<p>Search Engine Optimization (SEO) remains the most powerful and cost-effective digital marketing strategy for businesses in Delhi NCR. In 2024, the landscape has evolved significantly...</p><h2>1. Local SEO Optimization</h2><p>For businesses targeting customers in Delhi NCR, local SEO is paramount. Optimize your Google My Business profile, ensure consistent NAP (Name, Address, Phone) citations across directories, and focus on location-specific keywords.</p><h2>2. Core Web Vitals</h2><p>Google now uses Core Web Vitals as a ranking factor. Your website must load fast, be interactive quickly, and have visual stability. Aim for LCP under 2.5 seconds, FID under 100ms, and CLS under 0.1.</p>',
                'is_featured' => true,
                'sort_order'  => 1,
            ],
            [
                'title'    => 'How Google Ads Can Transform Your Business Growth in 2024',
                'slug'     => 'google-ads-business-growth-2024',
                'author'   => 'JayVisionX Team',
                'category' => 'PPC',
                'excerpt'  => 'Learn how a well-managed Google Ads campaign can generate instant leads, increase sales, and grow your business faster than any other digital channel.',
                'content'  => '<p>Google Ads (formerly Google AdWords) is one of the most powerful paid advertising platforms available to businesses today. With the right strategy, it can deliver immediate, measurable results...</p>',
                'is_featured' => true,
                'sort_order'  => 2,
            ],
            [
                'title'    => 'Why Your Business Needs a Mobile App in 2024',
                'slug'     => 'why-business-needs-mobile-app-2024',
                'author'   => 'JayVisionX Team',
                'category' => 'App Development',
                'excerpt'  => 'With over 750 million smartphone users in India, having a mobile app for your business is no longer optional. Discover why app development is a smart investment.',
                'content'  => '<p>India has over 750 million smartphone users, and this number continues to grow exponentially. For businesses looking to stay competitive and provide exceptional customer experiences, a mobile app is no longer a luxury—it\'s a necessity...</p>',
                'is_featured' => false,
                'sort_order'  => 3,
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::updateOrCreate(['slug' => $blog['slug']], array_merge($blog, ['is_active' => true]));
        }
    }
}
