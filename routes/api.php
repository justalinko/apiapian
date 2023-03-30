<?php

use App\Http\Controllers\BotController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/bot', [BotController::class, 'index']);
Route::get('/isbot/{ip}/{ua}', [BotController::class, 'index']);
Route::get('/ip2country' , [BotController::class, 'ip2countryJson']);
Route::get('/ip2country/{ip}' , [BotController::class, 'ip2countryJson']);
Route::get('/stats' , [BotController::class, 'stats']);