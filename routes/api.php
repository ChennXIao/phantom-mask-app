<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\OrderController;

Route::get('/pharmacies', [PharmacyController::class, 'index']);
Route::get('/pharmacies/{pharmacy}/masks', [PharmacyController::class, 'masks']);
Route::get('/pharmacies/filter-by-mask-count', [PharmacyController::class, 'filterByMaskCount']);
Route::get('/search', [PharmacyController::class, 'search']);
Route::patch('/pharmacies/{pharmacy}/masks/{mask}', [PharmacyController::class, 'updateMaskStock']);
Route::post('/pharmacies/{pharmacy}/masks/batch', [PharmacyController::class, 'batchUpsert']);

Route::get('/orders/top-spenders', [OrderController::class, 'topSpenders']);
Route::post('/orders', [OrderController::class, 'store']);
