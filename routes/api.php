<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetPurchaseController;
use App\Http\Controllers\AssetRmaController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\NetworkAssetController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserSessionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserSessionController::class, 'show']);
    Route::post('/logout', [UserSessionController::class, 'destroy']);
    Route::apiResource('offices', OfficeController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('assets', AssetController::class);
    Route::apiResource('asset-purchases', AssetPurchaseController::class);
    Route::apiResource('features', FeatureController::class);
    Route::apiResource('network-assets', NetworkAssetController::class);
    Route::apiResource('asset-rmas', AssetRmaController::class);
    Route::post('asset-rmas/{asset_rma}/tracks', [AssetRmaController::class, 'addTrack']);
});
