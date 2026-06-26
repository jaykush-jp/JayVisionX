<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\CaseStudyController;
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
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/newsletter', [ContactController::class, 'newsletter'])->name('newsletter');

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
        Route::resource('services', AdminServiceController::class);
        Route::post('services/{id}/toggle-status', [AdminServiceController::class, 'toggleStatus'])->name('services.toggle-status');

        // Projects
        Route::resource('projects', AdminProjectController::class);
        Route::post('projects/{id}/toggle-status', [AdminProjectController::class, 'toggleStatus'])->name('projects.toggle-status');

        // Case Studies
        Route::resource('case-studies', AdminCaseStudyController::class);

        // Blogs
        Route::resource('blogs', AdminBlogController::class);
        Route::post('blogs/{id}/toggle-status', [AdminBlogController::class, 'toggleStatus'])->name('blogs.toggle-status');

        // Testimonials
        Route::resource('testimonials', AdminTestimonialController::class);

        // FAQs
        Route::resource('faqs', AdminFaqController::class);

        // Contact Enquiries
        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{id}', [AdminContactController::class, 'show'])->name('contacts.show');
        Route::delete('contacts/{id}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
        Route::post('contacts/{id}/mark-read', [AdminContactController::class, 'markRead'])->name('contacts.mark-read');

        // Settings
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // Pages (Home, About SEO)
        Route::resource('pages', AdminPageController::class);
    });
});
