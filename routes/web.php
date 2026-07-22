<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 
*/

// Landing Page
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return view('welcome');
});

// Socialite Routes
Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\AuthController::class, 'socialRedirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\AuthController::class, 'socialCallback'])->name('social.callback');

// Secret Admin Portal
Route::get('/admin/superadmin', [AuthController::class, 'showSuperAdminLogin']);
Route::post('/admin/superadmin', [AuthController::class, 'superAdminLogin']);

// News Routes
Route::get('/api/live-news', [NewsController::class, 'fetchLiveNews']);
Route::get('/home', [NewsController::class, 'index']); // Same as /news conceptually
Route::get('/news', [NewsController::class, 'index']);
Route::get('/category/{category}', [NewsController::class, 'category']);
Route::get('/news/create', [NewsController::class, 'create']);
Route::post('/news', [NewsController::class, 'store']);
Route::get('/news/{id}', [NewsController::class, 'show']);
Route::post('/news/{id}/comment', [NewsController::class, 'storeComment']);

Route::middleware(['auth'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile/update', [ProfileController::class, 'update']);

    // Dashboards and Review Workflows
    Route::get('/author/dashboard', [NewsController::class, 'authorDashboard']);
    Route::get('/news/{id}/edit', [NewsController::class, 'editNews']);
    Route::post('/news/{id}/update', [NewsController::class, 'updateNews']);

    Route::get('/channel/dashboard', [NewsController::class, 'channelDashboard']);
    Route::post('/channel/review/{id}', [NewsController::class, 'channelReview']);
    Route::post('/channel/add-author', [NewsController::class, 'addAuthorToChannel']);
    Route::post('/channel/assign-task', [NewsController::class, 'assignTask']);

    Route::post('/author/task/complete/{id}', [NewsController::class, 'markTaskCompleted']);

    Route::get('/admin/dashboard', [NewsController::class, 'adminDashboard']);
    Route::post('/admin/review/{id}', [NewsController::class, 'adminReview']);
});

require __DIR__.'/auth.php';
