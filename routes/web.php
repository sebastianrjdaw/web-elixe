<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ContentBlockController as AdminContentBlockController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DiagnosticController as AdminDiagnosticController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\LegalPageController as AdminLegalPageController;
use App\Http\Controllers\Admin\ResponseTemplateController as AdminResponseTemplateController;
use App\Http\Controllers\Admin\ScreenController as AdminScreenController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SyncRunController as AdminSyncRunController;
use App\Http\Controllers\Web\LeadController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/pantallas', [PageController::class, 'screens'])->name('screens');
Route::get('/red-de-pantallas', [PageController::class, 'screens'])->name('screens.network');
Route::get('/asesoramiento', [PageController::class, 'advice'])->name('advice');
Route::post('/asesoramiento', [LeadController::class, 'storeAdvice'])->middleware('throttle:6,1')->name('advice.store');
Route::get('/locales', [PageController::class, 'venues'])->name('venues');
Route::post('/locales/solicitud', [LeadController::class, 'redirectLegacyVenue'])->middleware('throttle:6,1')->name('venues.request');
Route::get('/anunciantes', [PageController::class, 'advertisers'])->name('advertisers');
Route::post('/anunciantes/solicitud', [LeadController::class, 'redirectLegacyAdvertiser'])->middleware('throttle:6,1')->name('advertisers.request');
Route::get('/gracias', [PageController::class, 'thanks'])->name('thanks');
Route::get('/privacidad', [PageController::class, 'privacy'])->name('privacy');
Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');
Route::get('/aviso-legal', [PageController::class, 'legalNotice'])->name('legal.notice');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', fn () => response(
    "User-agent: *\nDisallow:\nSitemap: ".rtrim(config('app.url'), '/')."/sitemap.xml\n",
    200,
    ['Content-Type' => 'text/plain; charset=UTF-8'],
))->name('robots');

Route::prefix('gl')->name('gl.')->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/pantallas', [PageController::class, 'screens'])->name('screens');
    Route::get('/red-de-pantallas', [PageController::class, 'screens'])->name('screens.network');
    Route::get('/asesoramiento', [PageController::class, 'advice'])->name('advice');
    Route::post('/asesoramiento', [LeadController::class, 'storeAdvice'])->middleware('throttle:6,1')->name('advice.store');
    Route::get('/locales', [PageController::class, 'venues'])->name('venues');
    Route::get('/anunciantes', [PageController::class, 'advertisers'])->name('advertisers');
    Route::get('/gracias', [PageController::class, 'thanks'])->name('thanks');
    Route::get('/privacidad', [PageController::class, 'privacy'])->name('privacy');
    Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');
    Route::get('/aviso-legal', [PageController::class, 'legalNotice'])->name('legal.notice');
});

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/admin/login', [AdminAuthController::class, 'authenticate'])->middleware('throttle:6,1')->name('admin.login.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/export', [AdminLeadController::class, 'export'])->name('leads.export');
    Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
    Route::patch('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('/leads/{lead}/resend', [AdminLeadController::class, 'resend'])->name('leads.resend');
    Route::post('/leads/{lead}/response', [AdminLeadController::class, 'sendResponse'])->name('leads.response');
    Route::get('/screens', [AdminScreenController::class, 'index'])->name('screens.index');
    Route::post('/screens/sync', [AdminScreenController::class, 'sync'])->name('screens.sync');
    Route::patch('/screens/{screen}/hide', [AdminScreenController::class, 'hide'])->name('screens.hide');
    Route::patch('/screens/{screen}/show', [AdminScreenController::class, 'showPublicly'])->name('screens.show');
    Route::get('/sync-runs', [AdminSyncRunController::class, 'index'])->name('sync-runs.index');
    Route::get('/content', [AdminContentBlockController::class, 'index'])->name('content.index');
    Route::patch('/content/{contentBlock}', [AdminContentBlockController::class, 'update'])->name('content.update');
    Route::get('/faqs', [AdminFaqController::class, 'index'])->name('faqs.index');
    Route::post('/faqs', [AdminFaqController::class, 'store'])->name('faqs.store');
    Route::patch('/faqs/{faq}', [AdminFaqController::class, 'update'])->name('faqs.update');
    Route::get('/legal-pages', [AdminLegalPageController::class, 'index'])->name('legal-pages.index');
    Route::patch('/legal-pages/{legalPage}', [AdminLegalPageController::class, 'update'])->name('legal-pages.update');
    Route::get('/response-templates', [AdminResponseTemplateController::class, 'index'])->name('response-templates.index');
    Route::post('/response-templates', [AdminResponseTemplateController::class, 'store'])->name('response-templates.store');
    Route::patch('/response-templates/{responseTemplate}', [AdminResponseTemplateController::class, 'update'])->name('response-templates.update');
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings/{setting}', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('/diagnostics', [AdminDiagnosticController::class, 'index'])->name('diagnostics.index');
    Route::post('/diagnostics/run', [AdminDiagnosticController::class, 'run'])->name('diagnostics.run');
});
