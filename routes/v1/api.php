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


Route::group(['prefix' => 'v1'], function () {
    Route::post('/orders', [\App\V1\Controllers\OrderController::class, 'create']);
    Route::get('/provinces', [\App\V1\Controllers\ProvinceController::class, 'index']);
    Route::get('/provinces/{province}', [\App\V1\Controllers\ProvinceController::class, 'show']);
    Route::get('/districts', [\App\V1\Controllers\DistrictController::class, 'index']);
    Route::get('/districts/{district}', [\App\V1\Controllers\DistrictController::class, 'show']);
    Route::get('/wards', [\App\V1\Controllers\WardController::class, 'index']);
    Route::get('/wards/{ward}', [\App\V1\Controllers\WardController::class, 'show']);
    Route::get('/products', [\App\V1\Controllers\ProductController::class, 'index']);
    Route::get('/products/{slug}', [\App\V1\Controllers\ProductController::class, 'show']);
    Route::get('/attributes', [\App\V1\Controllers\AttributeController::class, 'index']);
    Route::get('/attributes/{attribute}', [\App\V1\Controllers\AttributeController::class, 'show']);
    Route::get('/variants', [\App\V1\Controllers\VariantController::class, 'index']);
    Route::get('/variants/search', [\App\V1\Controllers\VariantController::class, 'searchGroup']);
    Route::get('/variants/{variant}', [\App\V1\Controllers\VariantController::class, 'show']);
    Route::get('/attribute-groups', [\App\V1\Controllers\AttributeGroupController::class, 'index']);
    Route::get('/attribute-groups/{attribute_groups}', [\App\V1\Controllers\AttributeGroupController::class, 'show']);
    Route::get('/posts', [\App\V1\Controllers\PostController::class, 'index']);
    Route::get('/posts/{slug}', [\App\V1\Controllers\PostController::class, 'show']);
    Route::get('/post-groups', [\App\V1\Controllers\PostGroupController::class, 'index']);
    Route::get('/post-groups/{slug}', [\App\V1\Controllers\PostGroupController::class, 'show']);
    Route::get('/categories', [\App\V1\Controllers\CategoryController::class, 'index']);
    Route::get('/categories/header', [\App\V1\Controllers\CategoryController::class, 'getCategoryHeader']);
    Route::get('/categories/dashboard', [\App\V1\Controllers\CategoryController::class, 'getCategoryDashboard']);
    Route::get('/categories/{slug}', [\App\V1\Controllers\CategoryController::class, 'show']);
    Route::get('/pages', [\App\V1\Controllers\PageController::class, 'index']);
    Route::get('/pages/headers', [\App\V1\Controllers\PageController::class, 'getPageHeader']);

    Route::get('/pages/{slug}', [\App\V1\Controllers\PageController::class, 'show']);
    Route::get('/page-groups', [\App\V1\Controllers\PageGroupController::class, 'index']);
    Route::post('/check-stock', [\App\V1\Controllers\OrderController::class, 'checkStock']);
});
