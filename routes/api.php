<?php

use App\Http\Controllers\Public\PublicScreenController;
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

Route::get('/public/network-summary', [PublicScreenController::class, 'summary']);
Route::get('/public/screens', [PublicScreenController::class, 'index']);
Route::get('/public/screens/map', [PublicScreenController::class, 'index']);
Route::get('/public/screen-filters', [PublicScreenController::class, 'filters']);
