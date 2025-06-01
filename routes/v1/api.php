<?php

use App\V1\Controllers\AttributeController;
use App\V1\Controllers\AttributeGroupController;
use App\V1\Controllers\CategoryController;
use App\V1\Controllers\DistrictController;
use App\V1\Controllers\OrderController;
use App\V1\Controllers\PageController;
use App\V1\Controllers\PageGroupController;
use App\V1\Controllers\PostController;
use App\V1\Controllers\PostGroupController;
use App\V1\Controllers\ProductController;
use App\V1\Controllers\ProvinceController;
use App\V1\Controllers\SeoContentController;
use App\V1\Controllers\TicketController;
use App\V1\Controllers\VariantController;
use App\V1\Controllers\WardController;
use Illuminate\Support\Facades\Route;

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


Route::group(['prefix' => 'v1'], function () {
    Route::post('/orders', [OrderController::class, 'create']);
    Route::get('/provinces', [ProvinceController::class, 'index']);
    Route::get('/provinces/{province}', [ProvinceController::class, 'show']);
    Route::get('/districts', [DistrictController::class, 'index']);
    Route::get('/districts/{district}', [DistrictController::class, 'show']);
    Route::get('/wards', [WardController::class, 'index']);
    Route::get('/wards/{ward}', [WardController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);

    Route::get('/product-hots', [ProductController::class, 'hot']);
    Route::get('/product-news', [ProductController::class, 'new']);
    Route::get('/product-upcoming', [ProductController::class, 'upcoming']);
    Route::get('/product-uniforms', [ProductController::class, 'uniform']);
    Route::get('/products/{slug}/reviews', [ProductController::class, 'getReview']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/attributes', [AttributeController::class, 'index']);
    Route::get('/attributes/{attribute}', [AttributeController::class, 'show']);
    Route::get('/variants', [VariantController::class, 'index']);
    Route::get('/variants/search', [VariantController::class, 'searchGroup']);
    Route::get('/variants/{variant}', [VariantController::class, 'show']);
    Route::get('/attribute-groups', [AttributeGroupController::class, 'index']);
    Route::get('/attribute-groups/{attribute_groups}', [AttributeGroupController::class, 'show']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/post-news', [PostController::class, 'new']);
    Route::get('/post-hots', [PostController::class, 'hot']);

    Route::get('/posts/{slug}', [PostController::class, 'show']);
    Route::get('/post-groups', [PostGroupController::class, 'index']);
    Route::get('/post-groups/{slug}', [PostGroupController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
//    Route::get('/category-products', [\App\V1\Controllers\CategoryController::class, 'categoryProduct']);
    Route::get('/categories/all', [CategoryController::class, 'getCategoryAll']);
    Route::get('/categories/search-all', [CategoryController::class, 'getSearchAll']);
    Route::get('/categories/header', [CategoryController::class, 'getCategoryHeader']);
    Route::get('/categories/dashboard', [CategoryController::class, 'getCategoryDashboard']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/seo-contents', [SeoContentController::class, 'show']);
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/headers', [PageController::class, 'getPageHeader']);

    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::get('/page-groups', [PageGroupController::class, 'index']);
    Route::post('/check-stock', [OrderController::class, 'checkStock']);
    Route::post('/orders', [OrderController::class, 'create']);
    Route::post('/tickets', [TicketController::class, 'create']);
});
