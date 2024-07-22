<?php

use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

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

//Route Login NON SSO
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('auth/login', [AuthController::class, 'postlogin'])->name('postlogin')->middleware("throttle:5,2");
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => ['clear.permission.cache']], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Marketing
        include __DIR__.'/Marketing/inputPOCustomer.php';
        include __DIR__.'/Marketing/orderConfirmation.php';
        include __DIR__.'/Marketing/salesOrder.php';
    });
});

