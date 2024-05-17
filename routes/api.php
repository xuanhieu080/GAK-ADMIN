<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeGroupController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerRechargeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PageGroupController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostGroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

require __DIR__ . '/v1/api.php';

Route::post('/sanctum/token', TokenController::class);

Route::middleware(['auth:sanctum', 'apply_locale'])->group(function () {

    /**
     * Auth related
     */
    Route::get('/users/auth', AuthController::class);

    /**
     * Users
     */
    Route::put('/users/{user}/avatar', [UserController::class, 'updateAvatar']);
    Route::resource('users', UserController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('recharges', CustomerRechargeController::class);
    Route::get('orders/{id}/details', [OrderController::class, 'details']);
    Route::resource('orders', OrderController::class);
    Route::match(['put', 'patch'], '/products/{product}/attribute', [ProductController::class, 'attribute']);

    Route::resource('categories', CategoryController::class);
    Route::resource('attribute-groups', AttributeGroupController::class);
    Route::resource('attributes', AttributeController::class);
    Route::resource('posts', PostController::class);
    Route::resource('post-groups', PostGroupController::class);
    Route::resource('pages', PageController::class);
    Route::resource('page-groups', PageGroupController::class);
    Route::resource('supports', SupportController::class);
    Route::resource('configs', ConfigController::class);
    Route::post('/customers/{id}/recharge', [CustomerController::class, 'recharge']);
    Route::post('/upload-image', [ConfigController::class, 'uploadImage']);
    Route::get('/comments', [CommentController::class, 'index']);
    Route::get('/banks', [BankController::class, 'index']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    Route::resource('products', ProductController::class);
    Route::get('/products/{product}/attribute', [ProductController::class, 'getAttribute']);
    Route::get('/products/{product}/sync', [ProductController::class, 'productVariantSync']);
    Route::get('/products/{product}/variants', [ProductController::class, 'productVariant']);
    Route::get('/products/{product}/variant-mains', [ProductController::class, 'productVariantMain']);
    Route::match(['put', 'patch'], '/products/{product}/variants/{product_variant_main}', [ProductController::class, 'updateProductVariantMainItem']);
    Route::match(['put', 'patch'], '/products/{product}/variants', [ProductController::class, 'updateProductVariant']);
    Route::match(['put', 'patch'], '/products/{product}/highlight', [ProductController::class, 'updateHighlight']);
    Route::post('/products/{product}/reviews', [ProductController::class, 'createReview']);
    Route::get('/products/{product}/reviews', [ProductController::class, 'getReview']);
    Route::get('/products/{product}/reviews/{productReview}', [ProductController::class, 'getReviewItem']);
    Route::match(['put', 'patch'], '/products/{product}/reviews/{product_review}', [ProductController::class, 'updateReview']);
    Route::delete('/products/{product}/reviews/{product_review}', [ProductController::class, 'deleteReview']);
    Route::match(['put', 'patch'], '/products/product-variants/{product_variant}', [ProductController::class, 'updateProductVariantItem']);

    /**
     * Roles
     */
    Route::get('/roles/search', [RoleController::class, 'search'])->middleware('throttle:400,1');
});
