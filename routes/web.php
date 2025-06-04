<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\SeoIndexationController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\AnalyticController;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::feeds();

        Route::prefix('websites')->group(function () {
            Route::get('/', [WebsiteController::class, 'index'])->name('website.connected');
            Route::get('/access_websites', [WebsiteController::class, 'access_websites'])->name('website.connected');
            Route::get('/analytics', [WebsiteController::class, 'fetchAnalytics'])->name('websites.analytics');

            Route::post('/', [WebsiteController::class, 'addWebsite'])->name('website.addWebsite');
            Route::delete('/{website}', [WebsiteController::class, 'destroy'])->name('website.remove');
            Route::get('/connected', [WebsiteController::class, 'access_websites'])->name('website.connected');
            Route::post('/{website}/track', [WebsiteController::class, 'track'])->name('website.track');
            Route::get('/list', function () {
                return Inertia::render('Websites/Index');
            })->name('websites.index');
        });


        Route::prefix('realtime')->group(function () {
            Route::get('/', function () {
                return Inertia::render('Realtime/Index');
            })->name('realtime.index');
        });

        Route::prefix('heatmaps')->group(function () {
            Route::get('/', function () {
                return Inertia::render('Heatmaps/Index');
            })->name('heatmaps.index');
        });

        Route::prefix('analytics')->group(function () {
            Route::get('/pages-analytics', [AnalyticController::class, 'PageAnalytics'])->name('websites.pages.analytics');
            Route::get('/', function () {
                return Inertia::render('Analytics/Index');
            })->name('analytics.index');
        });

        Route::prefix('seo')->group(function () {
            Route::get('/', [SeoIndexationController::class, 'index'])->name('seo.index');
            Route::get('/path-indexation', [SeoIndexationController::class, 'indexation'])->name('seo.indexation');
            Route::get('/report/{id}', [SeoIndexationController::class, 'report'])->name('seo.report');
            Route::get('/recommendations/{id}', [SeoIndexationController::class, 'suggestions'])->name('seo.suggestions');
            Route::get('/', function () {
                return Inertia::render('SEO/Index');
            })->name('seo.index');
        });

        Route::prefix('keywords')->group(function () {
            Route::get('/', function () {
                return Inertia::render('Keywords/Index');
            })->name('keywords.index');
        });

        Route::prefix('settings')->group(function () {
            Route::get('/', function () {
                return Inertia::render('Settings/Index');
            })->name('settings.index');
        });

        Route::prefix('paths')->group(function () {
            Route::get('/', function () {
                return Inertia::render('Paths/Index');
            })->name('paths.index');
        });

        Route::get('/', [HomeController::class, 'index'])->name('welcome');
        Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->middleware(['auth', 'verified'])->name('dashboard');

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

        require __DIR__ . '/auth.php';
    }
);
