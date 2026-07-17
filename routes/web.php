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
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Landing Page
Route::get('/', function () {
    if (session('user_logged_in')) {
        return redirect('/home');
    }
    return view('welcome');
});

// Authentication Mock Routes
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);

// News Routes
Route::get('/home', [NewsController::class, 'index']); // Same as /news conceptually
Route::get('/news', [NewsController::class, 'index']);
Route::get('/category/{category}', [NewsController::class, 'category']);
Route::get('/news/create', [NewsController::class, 'create']);
Route::post('/news', [NewsController::class, 'store']);
Route::get('/news/{id}', [NewsController::class, 'show']);
Route::post('/news/{id}/comment', [NewsController::class, 'storeComment']);

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

Route::get('/admin/dashboard', [NewsController::class, 'adminDashboard']);
Route::post('/admin/review/{id}', [NewsController::class, 'adminReview']);
