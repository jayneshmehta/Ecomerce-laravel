<?php

use App\Http\Controllers\UsersController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/googlelogin', function () {
    return view('auth/google_login');
});

Route::get('auth/google', [UsersController::class, 'redirectToGoogle']);
Route::get('auth/callback/google', [UsersController::class, 'handleCallback']);


Route::get('/Orderview', function () {
    return view('Orderview');
});
Route::get('/VerifySuccess', function () {
    return view('VerifySuccess');
});
Route::get('/VerifyError', function () {
    return view('VerifyError');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
