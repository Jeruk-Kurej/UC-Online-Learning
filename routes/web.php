<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\UcTestimonyController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\FeaturedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\CollabController;
use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC ROUTES
// ============================================================

Route::get('/', function () {
    return redirect('/featured');
})->name('home');


Route::middleware(['throttle:showcase'])->group(function () {
    Route::get('/about', AboutController::class)->name('about');
    Route::get('/featured', [FeaturedController::class, 'index'])->name('featured');
    Route::get('/business', [BusinessController::class, 'index'])->name('businesses.index');
    Route::get('/uc-testimonies', [UcTestimonyController::class, 'index'])->name('uc-testimonies.index');
    Route::get('/google-drive-image/{id}', [UserController::class, 'proxyGoogleDriveImage'])->name('google-drive-image');
});

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/import-progress/{sessionId}', [ImportController::class, 'progress'])->name('import.progress');
    Route::get('/import-progress/check', [ImportController::class, 'checkActive'])->name('import.check');
    Route::post('/clear-active-import', [ImportController::class, 'clearActive'])->name('import.clear');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/{message}', [InboxController::class, 'show'])->name('inbox.show');

    // Collabs
    Route::post('/collabs/{user}', [CollabController::class, 'store'])->name('collabs.store');
    Route::post('/collabs/{collab}/accept', [CollabController::class, 'accept'])->name('collabs.accept');
    Route::post('/collabs/{collab}/reject', [CollabController::class, 'reject'])->name('collabs.reject');

    Route::get('/my-business', [BusinessController::class, 'my'])->name('businesses.my');
    Route::get('/business/create', [BusinessController::class, 'create'])->name('businesses.create');
    Route::post('/business', [BusinessController::class, 'store'])->name('businesses.store');
    Route::get('/business/{business}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
    Route::put('/business/{business}', [BusinessController::class, 'update'])->name('businesses.update');
    Route::get('/api/regencies', [BusinessController::class, 'getRegencies'])->name('api.regencies');

    Route::get('/my-testimony', [UcTestimonyController::class, 'my'])->name('uc-testimonies.my');
    Route::post('/uc-testimonies', [UcTestimonyController::class, 'store'])->name('uc-testimonies.store');

    // Products and Services CRUD
    Route::resource('business.products', ProductController::class)->parameters(['business' => 'business'])->names('businesses.products')->except(['index', 'show']);
    Route::resource('business.services', ServiceController::class)->parameters(['business' => 'business'])->names('businesses.services')->except(['index', 'show']);

    // Intrapreneur Achievements
    Route::get('/intrapreneurs/{company}/achievements/create', [BusinessController::class, 'createAchievement'])->name('intrapreneurs.create_achievement');
    Route::post('/intrapreneurs/{company}/achievements', [BusinessController::class, 'addAchievement'])->name('intrapreneurs.add_achievement');
    Route::delete('/intrapreneurs/{company}/achievements', [BusinessController::class, 'deleteAchievement'])->name('intrapreneurs.delete_achievement');

    // Intrapreneur / Company Management
    Route::get('/intrapreneurs/create', [BusinessController::class, 'createCompany'])->name('intrapreneurs.create');
    Route::post('/intrapreneurs', [BusinessController::class, 'storeCompany'])->name('intrapreneurs.store');
    Route::get('/intrapreneurs/{company}/edit', [BusinessController::class, 'editCompany'])->name('intrapreneurs.edit');
    Route::put('/intrapreneurs/{company}', [BusinessController::class, 'updateCompany'])->name('intrapreneurs.update');
});

// ============================================================
// ADMIN-ONLY ROUTES
// ============================================================

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    
    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('/users/stats', [UserController::class, 'stats'])->name('users.stats');
    Route::post('/users/{user}/toggle-featured', [UserController::class, 'toggleFeatured'])->name('users.toggle-featured');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    Route::post('/business/import', [BusinessController::class, 'import'])->name('businesses.import');
    Route::delete('/business/{business}', [BusinessController::class, 'destroy'])->name('businesses.destroy');
    Route::post('/business/{business}/toggle-featured', [BusinessController::class, 'toggleFeatured'])->name('businesses.toggle-featured');
    Route::get('/business', [BusinessController::class, 'adminIndex'])->name('businesses.admin');
    Route::post('/business/{business}/status', [BusinessController::class, 'updateStatus'])->name('businesses.update-status');
    Route::post('/intrapreneurs/{company}/status', [BusinessController::class, 'updateCompanyStatus'])->name('intrapreneurs.update-status');
    Route::post('/business/{business}/approve', [BusinessController::class, 'approve'])->name('businesses.approve');
    
    Route::get('/testimonies', [UcTestimonyController::class, 'adminIndex'])->name('uc-testimonies.admin');
    Route::post('/testimonies/{user:id}/toggle-featured', [UcTestimonyController::class, 'toggleFeatured'])->name('uc-testimonies.toggle-featured');

    Route::get('/pages/{page:slug}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page:slug}', [PageController::class, 'update'])->name('pages.update');
    Route::post('/pages/upload-image', [PageController::class, 'uploadImage'])->name('pages.upload-image');
});

Route::middleware(['throttle:showcase'])->group(function () {
    // Universal business resolver for incoming requests
    Route::get('/business/{slug}', [BusinessController::class, 'resolveShowcase'])->name('showcase.resolve');

    // These routes must be defined AFTER the resolver. They are used to preserve reverse routing (url generation) 
    // for existing Blade views without needing to refactor all route() calls.
    Route::get('/business/{company}', [BusinessController::class, 'showIntrapreneur'])->name('intrapreneurs.show');
    Route::get('/business/{business}', [BusinessController::class, 'show'])->name('businesses.show');
});

require __DIR__.'/auth.php';

// Catch-all route for user profiles (must be at the very bottom to avoid intercepting other routes like /login or /about)
Route::middleware(['throttle:showcase'])->group(function () {
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
});
