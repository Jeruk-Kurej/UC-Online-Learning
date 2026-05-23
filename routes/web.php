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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC ROUTES
// ============================================================

Route::get('/', function () {
    return redirect('/featured');
})->name('home');


Route::get('/about', AboutController::class)->name('about');
Route::get('/featured', [FeaturedController::class, 'index'])->name('featured');
Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.index');
Route::get('/intrapreneurs/{company}', [BusinessController::class, 'showIntrapreneur'])->name('intrapreneurs.show');
Route::get('/uc-testimonies', [UcTestimonyController::class, 'index'])->name('uc-testimonies.index');
Route::get('/google-drive-image/{id}', [UserController::class, 'proxyGoogleDriveImage'])->name('google-drive-image');

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

    Route::get('/my-businesses', [BusinessController::class, 'my'])->name('businesses.my');
    Route::get('/businesses/create', [BusinessController::class, 'create'])->name('businesses.create');
    Route::post('/businesses', [BusinessController::class, 'store'])->name('businesses.store');
    Route::get('/businesses/{business}/edit', [BusinessController::class, 'edit'])->name('businesses.edit');
    Route::put('/businesses/{business}', [BusinessController::class, 'update'])->name('businesses.update');
    Route::get('/api/regencies', [BusinessController::class, 'getRegencies'])->name('api.regencies');

    Route::get('/my-testimony', [UcTestimonyController::class, 'my'])->name('uc-testimonies.my');
    Route::post('/uc-testimonies', [UcTestimonyController::class, 'store'])->name('uc-testimonies.store');

    // Products and Services CRUD
    Route::resource('businesses.products', ProductController::class)->except(['index']);
    Route::resource('businesses.services', ServiceController::class)->except(['index', 'show']);

    // Intrapreneur Achievements
    Route::get('/intrapreneurs/{company}/achievements/create', [BusinessController::class, 'createAchievement'])->name('intrapreneurs.create_achievement');
    Route::post('/intrapreneurs/{company}/achievements', [BusinessController::class, 'addAchievement'])->name('intrapreneurs.add_achievement');
    Route::delete('/intrapreneurs/{company}/achievements', [BusinessController::class, 'deleteAchievement'])->name('intrapreneurs.delete_achievement');
});

// ============================================================
// ADMIN-ONLY ROUTES
// ============================================================

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    
    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('/users/stats', [UserController::class, 'stats'])->name('users.stats');
    Route::post('/users/{user}/toggle-featured', [UserController::class, 'toggleFeatured'])->name('users.toggle-featured');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    Route::post('/businesses/import', [BusinessController::class, 'import'])->name('businesses.import');
    Route::delete('/businesses/{business}', [BusinessController::class, 'destroy'])->name('businesses.destroy');
    Route::post('/businesses/{business}/toggle-featured', [BusinessController::class, 'toggleFeatured'])->name('businesses.toggle-featured');
    Route::get('/admin/businesses', [BusinessController::class, 'adminIndex'])->name('businesses.admin');
    Route::post('/businesses/{business}/status', [BusinessController::class, 'updateStatus'])->name('businesses.update-status');
    Route::post('/businesses/{business}/approve', [BusinessController::class, 'approve'])->name('businesses.approve');
    
    Route::get('/admin/testimonies', [UcTestimonyController::class, 'adminIndex'])->name('uc-testimonies.admin');
    Route::post('/admin/testimonies/{user}/toggle-featured', [UcTestimonyController::class, 'toggleFeatured'])->name('uc-testimonies.toggle-featured');
});

// This must be LAST to avoid matching static routes like 'create'
Route::get('/businesses/{business}', [BusinessController::class, 'show'])->name('businesses.show');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');



require __DIR__.'/auth.php';
