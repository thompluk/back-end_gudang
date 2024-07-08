<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrinsipalController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/notAuthenticated', [AuthController::class, 'notAuthenticated'])->name('notAuthenticated');
// Route::post('/createuser', [UserController::class, 'createuser'])->name('user.createuser');
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/profile', [UserController::class, 'getUserLoggedIn'])->name('user.getLogIn');
    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');

    // * User
    Route::get('/alluser', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::post('/user', [UserController::class, 'createuser'])->name('user.createuser');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    // * Prinsipal
    Route::get('/allprinsipal', [PrinsipalController::class, 'index'])->name('prinsipal.index');
    Route::get('/prinsipal/{id}', [PrinsipalController::class, 'show'])->name('prinsipal.show');
    Route::post('/prinsipal', [PrinsipalController::class, 'create'])->name('prinsipal.create');
    Route::put('/prinsipal/update/{id}', [PrinsipalController::class, 'update'])->name('prinsipal.update');
    Route::delete('/prinsipal/{id}', [PrinsipalController::class, 'destroy'])->name('prinsipal.destroy');

    // Route::get('/user/{role}', [UserController::class, 'getUserByRole'])->name('user.index');

});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
