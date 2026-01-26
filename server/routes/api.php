<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\PicController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductParameterController;
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
Route::get('comments', [CommentController::class, 'index']);

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

        // Kategória kezelés
        Route::post('categories', [CategoryController::class, 'store']);
        Route::patch('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

        // Cég kezelés
        Route::post('companies', [CompanyController::class, 'store']);
        Route::patch('companies/{company}', [CompanyController::class, 'update']);
        Route::delete('companies/{company}', [CompanyController::class, 'destroy']);

        // Paraméter kezelés
        Route::post('parameters', [ParameterController::class, 'store']);
        Route::patch('parameters/{parameter}', [ParameterController::class, 'update']);
        Route::delete('parameters/{parameter}', [ParameterController::class, 'destroy']);

        // Kép kezelés
        Route::post('pics', [PicController::class, 'store']);
        Route::patch('pics/{pic}', [PicController::class, 'update']);
        Route::delete('pics/{pic}', [PicController::class, 'destroy']);

        // Egység kezelés
        Route::post('units', [UnitController::class, 'store']);
        Route::patch('units/{unit}', [UnitController::class, 'update']);
        Route::delete('units/{unit}', [UnitController::class, 'destroy']);

        // Termék paraméter kezelés
        Route::post('product-parameters', [ProductParameterController::class, 'store']);
        Route::patch('product-parameters/{product_parameter}', [ProductParameterController::class, 'update']);
        Route::delete('product-parameters/{product_parameter}', [ProductParameterController::class, 'destroy']);

        // Komment kezelés (Admin is kezelhet)
        Route::get('comments/{comment}', [CommentController::class, 'show']);
        Route::patch('comments/{comment}', [CommentController::class, 'update']);
        Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

        // Kategóriák megtekintése
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{category}', [CategoryController::class, 'show']);

        // Cégek megtekintése
        Route::get('companies', [CompanyController::class, 'index']);
        Route::get('companies/{company}', [CompanyController::class, 'show']);

        // Paraméterek megtekintése
        Route::get('parameters', [ParameterController::class, 'index']);
        Route::get('parameters/{parameter}', [ParameterController::class, 'show']);

        // Képek megtekintése
        Route::get('pics', [PicController::class, 'index']);
        Route::get('pics/{pic}', [PicController::class, 'show']);

        // Egységek megtekintése
        Route::get('units', [UnitController::class, 'index']);
        Route::get('units/{unit}', [UnitController::class, 'show']);

        // Termék paraméterek megtekintése
        Route::get('product-parameters', [ProductParameterController::class, 'index']);
        Route::get('product-parameters/{product_parameter}', [ProductParameterController::class, 'show']);
    });

    // --- VÁSÁRLÓI JOGOK (Role=2 vagy mindenki más) ---
    // Ide tartozik a profilkezelés, kosár, és kommentelés
    Route::middleware('ability:customer')->group(function () {
        // Saját profil kezelés
        Route::get('usersme', [UserController::class, 'indexSelf']);
        Route::patch('usersme', [UserController::class, 'updateSelf']);
        Route::patch('usersmeupdatepassword', [UserController::class, 'updatePassword']);
        Route::delete('usersme', [UserController::class, 'destroySelf']);
        
        // Kosár kezelés
        Route::get('carts', [CartController::class, 'index']);
        Route::post('carts', [CartController::class, 'store']);
        Route::get('carts/{cart}', [CartController::class, 'show']);
        Route::patch('carts/{cart}', [CartController::class, 'update']);
        Route::delete('carts/{cart}', [CartController::class, 'destroy']);

        // Kosár tételek kezelés
        Route::get('cart-items', [CartItemController::class, 'index']);
        Route::post('cart-items', [CartItemController::class, 'store']);
        Route::get('cart-items/{cart_item}', [CartItemController::class, 'show']);
        Route::patch('cart-items/{cart_item}', [CartItemController::class, 'update']);
        Route::delete('cart-items/{cart_item}', [CartItemController::class, 'destroy']);

        // Komment kezelés (Vásárlók kommentelhetenek)
        Route::post('comments', [CommentController::class, 'store']);
    });
});