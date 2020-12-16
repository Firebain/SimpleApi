<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

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

Route::post("/auth/token", [AuthController::class, "token"])
    ->name("auth.token");

Route::get("/categories", [CategoryController::class, "index"])
    ->name("categories.index");
Route::middleware("auth:sanctum")
    ->apiResource('categories', CategoryController::class)
    ->except(["index", "show"]);

Route::get("/categories/{category}/products", [ProductController::class, "index"])
    ->name("categories.products.index");
Route::middleware("auth:sanctum")
    ->apiResource('categories.products', ProductController::class)
    ->except(["index", "show"])
    ->scoped();