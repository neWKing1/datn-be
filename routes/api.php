<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\BillDetailController;
use App\Http\Controllers\Api\BillHistoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\ImageGalleryController;
use App\Http\Controllers\Api\PaymentHistoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\ReturnProductController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VariantController;
use \App\Http\Controllers\Api\Client\ProductClientController;
use App\Http\Controllers\Api\VoucherController;
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

    /* Image Gallery routes */
    Route::group(['prefix' => 'image-gallery'], function () {
        Route::get('/{colorFolder}', [ImageGalleryController::class, 'getListPhotoByColor']);
        Route::resource('/', ImageGalleryController::class);
    });

    /* Promotion routes */
    Route::get('/promotion/list-shoe-id/{id}', [PromotionController::class, 'listShoeId']);
    Route::get('/promotion/list-shoe-detail-id/{id}', [PromotionController::class, 'listShoeDetailId']);
    Route::resource('/promotion', PromotionController::class);

    /* User routes */
    Route::resource('/users', UserController::class);

    /* Voucher routes */
    Route::get('/get-voucher', [VoucherController::class, 'getVoucher']);
    Route::get('/find-voucher', [VoucherController::class, 'findVoucher']);
    Route::resource('/voucher', VoucherController::class);

    /* Bill routes */
    Route::get('/get-top-bill', [BillController::class, 'getTopProductInOrder']);
    Route::get('/get-status-bill-today', [BillController::class, 'getStatusBillToday']);
    Route::get('/get-bill', [BillController::class, 'getBillNotActive']);
    Route::put('/bill/change-status/{id}', [BillController::class, 'changeStatus']);
    Route::put('/bill/update-status-bill-success-vnpay/{id}', [BillController::class, 'updateStatusBillSuccessVnPay']);
    Route::put('/bill/change-info/{id}', [BillController::class, 'changeInfo']);
    Route::resource('/bill', BillController::class);

    /* Bill Detail  routes */
    Route::put('/bill-detail/change-quantity/{id}', [BillDetailController::class, 'updateQtyQuickly']);
    Route::resource('/bill-detail', BillDetailController::class);

    /* Bill History  routes */
    Route::resource('/bill-history', BillHistoryController::class);

    /* Payment History  routes */
    Route::resource('/payment-history', PaymentHistoryController::class);

    /* Customer  routes */
    Route::get('/customer', [UserController::class, 'getCustomer']);
    Route::post('/customer', [UserController::class, 'createCustomer']);
    Route::get('/customer/{id}', [UserController::class, 'showCustomer']);

    /*Thong ke*/
    Route::post('/statistic', [\App\Http\Controllers\Api\StatisticController::class, 'index']);
    Route::post('/dashboard-statistic', [\App\Http\Controllers\Api\StatisticController::class, 'orderToday']);
    Route::post('/revenue', [\App\Http\Controllers\Api\StatisticController::class, 'revenue']);

    /* Return Product routes */
    Route::resource('return-product', ReturnProductController::class);
});

/*Product client routes*/
Route::get('product-list', [ProductClientController::class, 'index']);
Route::get('product-detail/{slug}', [ProductClientController::class, 'detail']);
Route::get('product-attributes/{slug}', [ProductClientController::class, 'attributes']);
Route::get('product-range-price', [ProductClientController::class, 'rangePrice']);

/*Cart*/
Route::post('cart', [\App\Http\Controllers\Api\Client\CartController::class, 'index']);
Route::post('coupon', [\App\Http\Controllers\Api\Client\CartController::class, 'coupon']);
Route::get('vouchers', [\App\Http\Controllers\Api\Client\VoucherController::class, 'index']);

// payment
Route::get('payment-method', [\App\Http\Controllers\Api\Client\PaymentController::class, 'payments']);
Route::post('checking-payment', [\App\Http\Controllers\Api\Client\PaymentController::class, 'checkPayment']);

/*Delivery*/
Route::resource('delivery', \App\Http\Controllers\Api\Client\DeliveryController::class);

// Order
Route::get('order-status', [\App\Http\Controllers\Api\Client\OrderController::class, 'status']);
Route::resource('/order', \App\Http\Controllers\Api\Client\OrderController::class);
Route::post('return_order/{id}', [\App\Http\Controllers\Api\Client\OrderController::class, 'return_order']);
Route::post('order-payment', [\App\Http\Controllers\Api\Client\PaymentController::class, 'orderPayment']);
