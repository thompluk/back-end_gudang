<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermintaanPembelianBarangController;
use App\Http\Controllers\PermintaanPembelianBarangDetailController;
use App\Http\Controllers\PrinsipalController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/notAuthenticated', [AuthController::class, 'notAuthenticated'])->name('notAuthenticated');
Route::post('/createuser', [UserController::class, 'createuser'])->name('user.createuser');
Route::post('/login', [AuthController::class, 'login'])->name('login');




Route::middleware('auth:sanctum')->group(function () {
    
    // Route::get('/profile', [UserController::class, 'getUserLoggedIn'])->name('user.getLogIn');
    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');

    // * User
    Route::get('/alluser', [UserController::class, 'index'])->name('user.index');
    Route::get('/userSelect', [UserController::class, 'userSelect'])->name('user.userSelect');
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

    // * PermintaanPembelianBarang
    Route::get('/allppb', [PermintaanPembelianBarangController::class, 'index'])->name('ppb.index');
    Route::get('/allppb/draft', [PermintaanPembelianBarangController::class, 'indexDraft'])->name('ppb.indexDraft');
    Route::get('/allppb/onApproval', [PermintaanPembelianBarangController::class, 'indexOnApproval'])->name('ppb.indexOnApproval');
    Route::get('/allppb/done', [PermintaanPembelianBarangController::class, 'indexDone'])->name('ppb.indexDone');
    Route::get('/allppb/rejected', [PermintaanPembelianBarangController::class, 'indexRejected'])->name('ppb.indexRejected');
    Route::get('/ppb/{id}', [PermintaanPembelianBarangController::class, 'show'])->name('ppb.show');
    Route::post('/ppb', [PermintaanPembelianBarangController::class, 'create'])->name('ppb.create');
    Route::post('/ppb/post/{id}', [PermintaanPembelianBarangController::class, 'post'])->name('ppb.post');
    Route::put('/ppb/update/{id}', [PermintaanPembelianBarangController::class, 'update'])->name('ppb.update');
    Route::delete('/ppb/{id}', [PermintaanPembelianBarangController::class, 'destroy'])->name('ppb.destroy');

     // * PermintaanPembelianBarangDetail
     Route::get('/allppbdetail', [PermintaanPembelianBarangDetailController::class, 'index'])->name('ppbdetail.index');
     Route::get('/ppbdetaillist/{id}', [PermintaanPembelianBarangDetailController::class, 'showbyppbid'])->name('ppbdetail.showbyppbid');
     Route::get('/ppbdetail/{id}', [PermintaanPembelianBarangDetailController::class, 'show'])->name('ppbdetail.show');
     Route::post('/ppbdetail', [PermintaanPembelianBarangDetailController::class, 'create'])->name('ppbdetail.create');
     Route::put('/ppbdetail/update/{id}', [PermintaanPembelianBarangDetailController::class, 'update'])->name('ppbdetail.update');
     Route::delete('/ppbdetail/{id}', [PermintaanPembelianBarangDetailController::class, 'destroy'])->name('ppbdetail.destroy');
     Route::post('/saveAll/{ppb_id}', [PermintaanPembelianBarangDetailController::class, 'saveAll']);

    // Route::get('/user/{role}', [UserController::class, 'getUserByRole'])->name('user.index');
    Route::get('/allindexApproval', [ApprovalController::class, 'indexApproval'])->name('indexApproval');
    Route::post('/approval/approve', [ApprovalController::class, 'approve'])->name('approve');
    Route::post('/approval/reject', [ApprovalController::class, 'reject'])->name('reject');
    Route::post('/approval/return', [ApprovalController::class, 'return'])->name('return');

});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
