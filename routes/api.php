<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;

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

Route::post("/auth/token", [AuthController::class, "token"]);

Route::get("/categories", [CategoryController::class, "index"]);
Route::middleware("auth:sanctum")
    ->apiResource('categories', CategoryController::class)
    ->except(["index", "show"]);

Route::group(["middleware" => ["auth:sanctum"]], function () {
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });