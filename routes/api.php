<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\ImageGalleryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\VariantController;
use \App\Http\Controllers\Api\Client\ProductClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:owner'])->group(function () {
    Route::resource('/size', SizeController::class);
    Route::resource('/color', ColorController::class);
    Route::post('/add-variant', [VariantController::class, 'addVariants']);
    Route::resource('/variant', VariantController::class);

    /* Product routes */
    Route::get('/list-product-manage', [ProductController::class, 'listProductManage']);
    Route::get('/product-detail', [ProductController::class, 'productDetail']);
    Route::get('/product-detail-edit/{id}', [ProductController::class, 'productDetailEdit']);
    Route::put('/product-detail-edit/update-fast', [ProductController::class, 'updateFast']);
    Route::put('/product-detail-edit/{id}', [ProductController::class, 'updateProductDetail']);
    Route::put('/product/change-status', [ProductController::class, 'changeStatus']);
    Route::resource('/product', ProductController::class);

    /*Product client routes*/
    Route::get('product-list', [ProductClientController::class, 'index']);
    Route::get('product-detail/{slug}', [ProductClientController::class, 'detail']);
    Route::get('product-attributes/{slug}', [ProductClientController::class, 'attributes']);
    Route::get('product-range-price', [ProductClientController::class, 'rangePrice']);

    /*Cart*/
    Route::post('cart', [\App\Http\Controllers\Api\Client\CartController::class, 'index']);
    Route::post('coupon', [\App\Http\Controllers\Api\Client\CartController::class, 'coupon']);

    /* Image Gallery routes */
    Route::group(['prefix' => 'image-gallery'], function () {
        Route::get('/{colorFolder}', [ImageGalleryController::class, 'getListPhotoByColor']);
        Route::resource('/', ImageGalleryController::class);
    });

    /* Promotion routes */
    Route::get('/promotion/list-shoe-id/{id}', [PromotionController::class, 'listShoeId']);
    Route::get('/promotion/list-shoe-detail-id/{id}', [PromotionController::class, 'listShoeDetailId']);
    Route::resource('/promotion', PromotionController::class);
});
