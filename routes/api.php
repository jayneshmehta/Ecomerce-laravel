<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WishlistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UsersController::class)->group(function () {
    
    // For User Api
    Route::post('/senddata', 'me')->middleware('log.route');

    Route::get('/user/getUsers', 'index');

    Route::get('/verifyUser-{token}', 'verifyToken')->middleware('auth:sanctum');

    Route::get('/user/GettingUserById-{id}', 'show')->middleware('auth:sanctum');

    Route::post('/register', 'store');

    Route::post('/adduser', 'addUser')->middleware('auth:sanctum');

    Route::post('/login', 'loginUser');

    Route::post('/admin/login', 'adminLogin');

    Route::post('/sendmail', 'sendEmail');

    Route::post('/sendsmsotp', 'sendsmsotp');

    Route::post('/verifyOtp', 'verifyOtp');

    Route::post('/verifysmsotp', 'verifySmsOtp');

    Route::post('/changePassword', 'ChangePassword');

    Route::post('/UpdateUserbyid-{user}', 'update')->middleware('auth:sanctum');

    Route::post('/UpdateuserStatus-{user}', 'changeStatus')->middleware('auth:sanctum');

    Route::post('/changePrivilege-{user}', 'changePrivilege')->middleware('auth:sanctum');

    Route::delete('user/DeletingUserById-{user}', 'destroy')->middleware('auth:sanctum');

    Route::get('auth/google', 'redirectToGoogle');

    Route::get('auth/callback/google', 'handleCallback');

    Route::get('logout-{id}', 'logout');
});

// product's api

Route::controller(ProductController::class)->group(function () {

    Route::get('products', 'index');

    Route::get('productsWithSub_category', 'productsWithSub_category');

    Route::post('products', 'store');

    Route::get("products/GettingProductById-{id}", 'show');

    Route::post("products/UpdateProductById-{id}", 'update')->middleware('auth:sanctum');

    Route::get("products/GettingProductByCategoryId-{id}", 'getProductByCategory');

    Route::get("products/GettingProductBySub_CategoryId-{id}", 'getProductBySub_Category');

    Route::delete('products/DeletingProductById-{user}',  'destroy')->middleware("auth:sanctum");
});

// order's api

Route::controller(OrderController::class)->group(function () {

    Route::post('orders', 'store')->middleware('auth:sanctum');

    Route::get('orders', 'index')->middleware('auth:sanctum');

    Route::post('UpdateOrders-{order}', 'update')->middleware('auth:sanctum');

    Route::get('getOrdersByUser-{user}', 'userOrders')->middleware('auth:sanctum');

    Route::get('getOrdersBygroupId-{groupId}', 'ordersByGroupId')->middleware('auth:sanctum');

    Route::post('UpdateStatus-{id}', 'updateStatus')->middleware('auth:sanctum');

    Route::delete('DeletingOrderById-{order}', 'destroy')->middleware('auth:sanctum');
});

// category's api

Route::get('categorys', [CategoryController::class, 'index']);

// sub_category's api

Route::get('sub_category', [SubCategoryController::class, 'index']);

Route::get('getSub_categoryByCategoryId-{id}', [SubCategoryController::class, 'show'])->middleware('auth:sanctum');

// Wishlist api

Route::post('addOrCreate', [WishlistController::class, 'store'])->middleware('auth:sanctum');

Route::post('removeWishlist', [WishlistController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('getWishlistByUserId-{id}', [WishlistController::class, 'getWishlistByUserId'])->middleware('auth:sanctum');


// Coupans route

Route::get("getCouponsByName-{name}", [CouponsController::class, 'show'])->middleware('auth:sanctum');

Route::get("getCoupons", [CouponsController::class, 'index'])->middleware('auth:sanctum');

Route::post('/UpdatecouponStatus-{coupon}', [CouponsController::class, 'changeStatus'])->middleware('auth:sanctum');

Route::post('/addCoupons', [CouponsController::class, 'store'])->middleware('auth:sanctum');

Route::get('/GettingCouponById-{id}', [CouponsController::class, 'showbyId'])->middleware('auth:sanctum');

Route::post('/UpdateCouponbyid-{coupon}', [CouponsController::class, 'update'])->middleware('auth:sanctum');

Route::delete('/deleteCouponbyid-{coupon}', [CouponsController::class, 'destroy'])->middleware('auth:sanctum');

// Review route 

Route::post('addReview', [ReviewController::class, 'store'])->middleware('auth:sanctum');

Route::get('review/GettingreviewBproductId-{id}', [ReviewController::class, 'show']);

Route::get('notificationById-{id}', [NotificationsController::class, 'show']);
