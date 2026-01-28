<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\PicController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\ProductParameterController;

// Alap teszt endpoint
Route::get('/x', function () {
    return 'Webshop API 2026';
});

// --- REGION: AUTH & USER ---
Route::post('users/login', [UserController::class, 'login']);
Route::post('users', [UserController::class, 'store']); // Regisztráció
Route::post('users/logout', [UserController::class, 'logout'])
    ->middleware('auth:sanctum');

// Saját profil (usersme)
Route::get('usersme', [UserController::class, 'indexSelf'])
    ->middleware(['auth:sanctum', 'ability:usersme:get']);
Route::patch('usersme', [UserController::class, 'updateSelf'])
    ->middleware(['auth:sanctum', 'ability:usersme:patch']);
Route::patch('usersmeupdatepassword', [UserController::class, 'updatePassword'])
    ->middleware(['auth:sanctum', 'ability:usersme:updatePassword']);
Route::delete('usersme', [UserController::class, 'destroySelf'])
    ->middleware(['auth:sanctum', 'ability:usersme:delete']);

// Admin User kezelés
Route::get('users', [UserController::class, 'index'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::get('users/{id}', [UserController::class, 'show'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::patch('users/{id}', [UserController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::delete('users/{id}', [UserController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:admin']);


// --- REGION: PRODUCTS ---
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::post('products', [ProductController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::patch('products/{id}', [ProductController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::delete('products/{id}', [ProductController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:admin']);


// --- REGION: COMPANIES ---
Route::get('companies', [CompanyController::class, 'index'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::get('companies/{id}', [CompanyController::class, 'show'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::post('companies', [CompanyController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::patch('companies/{id}', [CompanyController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::delete('companies/{id}', [CompanyController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:admin']);


// --- REGION: CATEGORIES ---
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);
Route::post('categories', [CategoryController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::patch('categories/{id}', [CategoryController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::delete('categories/{id}', [CategoryController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:admin']);


// --- REGION: UNITS ---
Route::get('units', [UnitController::class, 'index']);
Route::get('units/{id}', [UnitController::class, 'show']);
Route::post('units', [UnitController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::patch('units/{id}', [UnitController::class, 'update'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::delete('units/{id}', [UnitController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:admin']);


// --- REGION: PARAMETERS & PRODUCT PARAMETERS ---
Route::get('parameters', [ParameterController::class, 'index']);
Route::get('parameters/{id}', [ParameterController::class, 'show']);
Route::post('parameters', [ParameterController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:admin']);

Route::get('product-parameters', [ProductParameterController::class, 'index']);
Route::post('product-parameters', [ProductParameterController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:admin']);


// --- REGION: PICS ---
Route::get('pics', [PicController::class, 'index']);
Route::post('pics', [PicController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:admin']);
Route::delete('pics/{id}', [PicController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:admin']);


// --- REGION: COMMENTS ---
Route::get('comments', [CommentController::class, 'index']);
Route::post('comments', [CommentController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:customer']); // Csak vásárlóknak
Route::delete('comments/{id}', [CommentController::class, 'destroy'])
    ->middleware(['auth:sanctum']); // Admin vagy a sajátját a tulajdonos


// --- REGION: CART & CART ITEMS ---
Route::get('carts', [CartController::class, 'index'])
    ->middleware(['auth:sanctum', 'ability:customer']);
Route::post('carts', [CartController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:customer']);

Route::get('cart-items', [CartItemController::class, 'index'])
    ->middleware(['auth:sanctum', 'ability:customer']);
Route::post('cart-items', [CartItemController::class, 'store'])
    ->middleware(['auth:sanctum', 'ability:customer']);
Route::delete('cart-items/{id}', [CartItemController::class, 'destroy'])
    ->middleware(['auth:sanctum', 'ability:customer']);