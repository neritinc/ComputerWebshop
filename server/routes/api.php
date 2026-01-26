<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Alap teszt endpoint
Route::get('/x', function(){
    return 'API';
});

// --- REGION: NYILVÁNOS (Bárki elérheti) ---
Route::post('users/login', [UserController::class, 'login']);
Route::post('users', [UserController::class, 'store']); // Regisztráció
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);

// --- REGION: AUTHENTICATED (Bejelentkezett felhasználók) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Kijelentkezés minden bejelentkezettnek
    Route::post('users/logout', [UserController::class, 'logout']);

    // --- ADMIN JOGOK (Csak Admin érheti el: role=1) ---
    Route::middleware('ability:admin')->group(function () {
        // User kezelés
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::patch('users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        // Termék kezelés (CRUD többi része)
        Route::post('products', [ProductController::class, 'store']);
        Route::patch('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
    });

    // --- VÁSÁRLÓI JOGOK (Role=2 vagy mindenki más) ---
    // Ide tartozik a profilkezelés és később a rendelés
    Route::middleware('ability:customer')->group(function () {
        Route::get('usersme', [UserController::class, 'indexSelf']);
        Route::patch('usersme', [UserController::class, 'updateSelf']);
        Route::patch('usersmeupdatepassword', [UserController::class, 'updatePassword']);
        Route::delete('usersme', [UserController::class, 'destroySelf']);
        
        // Ide írhatod majd a rendelés leadását:
        // Route::post('orders', [OrderController::class, 'store']);
    });
});