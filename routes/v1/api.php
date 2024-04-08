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
    Route::get('/pages', [\App\V1\Controllers\PageController::class, 'index']);
    Route::get('/pages/headers', [\App\V1\Controllers\PageController::class, 'getPageHeader']);
    Route::get('/pages/{page}', [\App\V1\Controllers\PageController::class, 'show']);
    Route::get('/page-groups', [\App\V1\Controllers\PageGroupController::class, 'index']);
});
