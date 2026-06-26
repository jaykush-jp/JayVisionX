# JayVisionX — Complete Laravel Website
### Digital Marketing Agency & Website Development Company

---

## 🚀 QUICK SETUP GUIDE

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL 8.0+
- Node.js >= 16 (optional, for npm assets)
- Apache / Nginx / Laravel Valet

---

## 📁 PROJECT STRUCTURE

```
jayvisionx/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          ← All admin panel controllers
│   │   │   └── Frontend/       ← All frontend page controllers
│   │   └── Middleware/
│   │       └── AdminAuth.php   ← Admin session authentication
│   └── Models/                 ← All Eloquent models
├── database/
│   ├── migrations/             ← All database tables
│   └── seeders/
│       └── DatabaseSeeder.php  ← Seeds all initial data
├── public/
│   ├── css/
│   │   ├── style.css           ← Main frontend styles
│   │   └── admin.css           ← Admin panel styles
│   ├── js/
│   │   ├── app.js              ← Frontend JavaScript
│   │   └── admin.js            ← Admin JavaScript
│   └── images/
│       └── hero-visual.svg     ← Hero section graphic
├── resources/views/
│   ├── frontend/               ← All frontend Blade templates
│   │   ├── layouts/app.blade.php ← Master layout with header/footer
│   │   ├── home/               ← Home page
│   │   ├── about/              ← About page
│   │   ├── services/           ← Services index + detail
│   │   ├── projects/           ← Projects index + detail
│   │   ├── blog/               ← Blog index + detail
│   │   ├── our-work/           ← Case studies
│   │   └── contact/            ← Contact page
│   └── admin/                  ← All admin panel Blade templates
│       ├── layouts/app.blade.php ← Admin master layout
│       ├── auth/login.blade.php
│       ├── dashboard/
│       ├── services/           ← CRUD for services
│       ├── projects/           ← CRUD for projects
│       ├── blogs/              ← CRUD for blog posts
│       ├── testimonials/       ← CRUD for testimonials
│       ├── faqs/               ← CRUD for FAQs
│       ├── contacts/           ← View enquiries
│       ├── case-studies/       ← CRUD for case studies
│       ├── pages/              ← SEO management
│       └── settings/           ← Website settings
└── routes/web.php              ← All routes
```

---

## ⚙️ STEP-BY-STEP INSTALLATION

### Step 1: Copy Files
```bash
# Copy the jayvisionx folder to your web server directory
# For XAMPP: C:/xampp/htdocs/jayvisionx
# For Linux: /var/www/html/jayvisionx
```

### Step 2: Install Dependencies
```bash
cd jayvisionx
composer install
```

### Step 3: Environment Setup
```bash
# Copy .env.example to .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jayvisionx_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Admin credentials
ADMIN_EMAIL=admin@jayvisionx.com
ADMIN_PASSWORD=JayVisionX@2024

# Company info
WHATSAPP_NUMBER=919999999999
COMPANY_PHONE=+91-9999999999
COMPANY_EMAIL=info@jayvisionx.com
```

### Step 5: Create Database
```sql
-- In MySQL/phpMyAdmin:
CREATE DATABASE jayvisionx_db;
```

### Step 6: Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Seed initial data (services, FAQs, testimonials, blog posts, settings)
php artisan db:seed

# Or do both in one command:
php artisan migrate --seed
```

### Step 7: Storage Link
```bash
php artisan storage:link
```

### Step 8: Copy Your Logo
```bash
# Copy your JayVisionX logo to:
public/images/logo.png

# Or upload it via Admin Panel → Website Settings → Site Logo
```

### Step 9: Run the Application
```bash
php artisan serve
# Access at: http://localhost:8000
```

---

## 🔐 ADMIN PANEL ACCESS

```
URL:      http://localhost:8000/admin/login
Email:    admin@jayvisionx.com
Password: JayVisionX@2024
```

**To change admin credentials**, update `.env`:
```env
ADMIN_EMAIL=your@email.com
ADMIN_PASSWORD=YourNewPassword@123
```

---

## 🌐 FRONTEND URLS

| Page | URL |
|------|-----|
| Home | `/` |
| About | `/about` |
| Services | `/services` |
| Service Detail | `/services/{slug}` |
| Projects | `/projects` |
| Project Detail | `/projects/{slug}` |
| Our Work | `/our-work` |
| Blog | `/blog` |
| Blog Post | `/blog/{slug}` |
| Contact | `/contact` |

---

## 🛠️ ADMIN PANEL MODULES

| Module | URL |
|--------|-----|
| Dashboard | `/admin/dashboard` |
| Services | `/admin/services` |
| Projects | `/admin/projects` |
| Case Studies | `/admin/case-studies` |
| Blogs | `/admin/blogs` |
| Testimonials | `/admin/testimonials` |
| FAQs | `/admin/faqs` |
| Pages & SEO | `/admin/pages` |
| Contact Enquiries | `/admin/contacts` |
| Website Settings | `/admin/settings` |

---

## 🗄️ DATABASE TABLES

| Table | Description |
|-------|-------------|
| `settings` | All website settings (logo, contact, social, SEO) |
| `services` | Services with slug, description, icon, meta |
| `projects` | Portfolio projects with gallery, technologies |
| `blogs` | Blog posts with SEO, views counter |
| `testimonials` | Client testimonials with ratings |
| `faqs` | FAQs with categories |
| `contacts` | Contact form enquiries |
| `case_studies` | Our work / case studies |
| `pages` | SEO settings for each page |
| `newsletters` | Newsletter subscribers |

---

## 🎨 COLOR THEME (from JayVisionX Logo)

```css
--primary:       #1E5EFF;   /* Electric Blue */
--primary-dark:  #1245CC;   /* Deep Blue */
--accent:        #FF6B35;   /* Orange */
--dark:          #0A1628;   /* Dark Navy */
--silver:        #8B9BB4;   /* Silver Grey */
--white:         #FFFFFF;
```

---

## 📦 PRODUCTION DEPLOYMENT

### For cPanel/Shared Hosting:

1. Upload all files to `public_html` or subdirectory
2. Move `public/` folder contents to root, update `index.php`:
   ```php
   require __DIR__.'/../../bootstrap/autoload.php';
   $app = require_once __DIR__.'/../../bootstrap/app.php';
   ```
3. Set `.env` `APP_ENV=production` and `APP_DEBUG=false`
4. Run: `php artisan config:cache && php artisan route:cache`

### For VPS/Server:

```bash
# Clone / upload project
composer install --optimize-autoloader --no-dev

# Set permissions
chmod -R 775 storage bootstrap/cache

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔧 APACHE .htaccess (public folder)

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 📧 EMAIL CONFIGURATION (Optional)

For contact form email notifications, add to `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@jayvisionx.com
MAIL_FROM_NAME="JayVisionX"
```

---

## 🔍 SEO FEATURES INCLUDED

- ✅ Dynamic meta title, description, keywords per page
- ✅ Open Graph tags for social sharing
- ✅ Twitter Card tags
- ✅ Organization Schema markup
- ✅ LocalBusiness Schema
- ✅ FAQ Schema (JSON-LD)
- ✅ Clean slug-based URLs
- ✅ Canonical URL tags
- ✅ Sitemap-ready URL structure
- ✅ Google Analytics integration
- ✅ Google Tag Manager integration

---

## 📱 FEATURES SUMMARY

### Frontend
- ✅ Fully responsive (mobile-first)
- ✅ Professional dark blue + electric blue + orange theme
- ✅ Hero section with animated stats counter
- ✅ Mega dropdown services menu
- ✅ Services grid with hover effects
- ✅ About preview section
- ✅ Tech stack showcase
- ✅ Portfolio/Projects grid
- ✅ Why Choose Us section
- ✅ Animated results/numbers section
- ✅ 4-step process section
- ✅ Testimonials Swiper carousel
- ✅ Blog grid with categories
- ✅ FAQ accordion with Schema
- ✅ CTA sections
- ✅ Floating WhatsApp + Call buttons
- ✅ Back to top button
- ✅ Footer with newsletter form

### Admin Panel
- ✅ Secure session-based login
- ✅ Dashboard with stats + recent enquiries
- ✅ Full CRUD for Services, Projects, Blogs
- ✅ Testimonials & FAQs management
- ✅ Contact enquiries viewer with reply links
- ✅ Per-page SEO management
- ✅ Website settings with logo upload
- ✅ Image uploads for all content
- ✅ Toggle active/inactive status
- ✅ Sort order management

---

## 💡 CUSTOMIZATION TIPS

1. **Change WhatsApp number**: Admin → Settings → WhatsApp Number
2. **Update logo**: Admin → Settings → Site Logo
3. **Add services**: Admin → Services → Add Service
4. **Change hero text**: Admin → Settings → Hero Section tab
5. **Update social links**: Admin → Settings → Social Media tab
6. **Add Google Analytics**: Admin → Settings → Analytics tab
7. **Manage SEO per page**: Admin → Pages & SEO

---

**Built with ❤️ by JayVisionX Team**
*See Beyond. Create Impact.*
