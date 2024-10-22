<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuktiPengeluaranBarangController;
use App\Http\Controllers\BuktiPengeluaranBarangDetailController;
use App\Http\Controllers\BuktiPengeluaranBarangDetailDetailController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PengembalianBarangController;
use App\Http\Controllers\PengembalianBarangDetailController;
use App\Http\Controllers\PermintaanPembelianBarangController;
use App\Http\Controllers\PermintaanPembelianBarangDetailController;
use App\Http\Controllers\PrinsipalController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderDetailController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\StockMaterialController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\SuratJalanDetailController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/notAuthenticated', [AuthController::class, 'notAuthenticated'])->name('notAuthenticated');
Route::post('/createuser', [UserController::class, 'createuser'])->name('user.createuser');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/allusertestserver', [UserController::class, 'index'])->name('user.index');



Route::middleware('auth:sanctum')->group(function () {
    
    // Route::get('/profile', [UserController::class, 'getUserLoggedIn'])->name('user.getLogIn');
    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');

    // * User
    Route::get('/alluser', [UserController::class, 'index'])->name('user.index');
    Route::post('/userSelect', [UserController::class, 'userSelect'])->name('user.userSelect');
    Route::post('/userSelectInventory', [UserController::class, 'userSelectInventory'])->name('user.userSelectInventory');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::post('/user', [UserController::class, 'createuser'])->name('user.createuser');
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/upload-excel', [UserController::class, 'upload'])->name('user.upload');
    Route::get('/user-download-template', [UserController::class, 'downloadTemplate'])->name('user.downloadTemplate');

    // * Prinsipal
    Route::get('/allprinsipal', [PrinsipalController::class, 'index'])->name('prinsipal.index');
    Route::get('/prinsipal/{id}', [PrinsipalController::class, 'show'])->name('prinsipal.show');
    Route::post('/prinsipal', [PrinsipalController::class, 'create'])->name('prinsipal.create');
    Route::put('/prinsipal/update/{id}', [PrinsipalController::class, 'update'])->name('prinsipal.update');
    Route::delete('/prinsipal/{id}', [PrinsipalController::class, 'destroy'])->name('prinsipal.destroy');

    // * Companies
    Route::get('/allcompanies', [CompaniesController::class, 'index'])->name('companies.index');
    Route::post('/companiesSelect', [CompaniesController::class, 'index'])->name('companies.index');
    Route::get('/companies/{id}', [CompaniesController::class, 'show'])->name('companies.show');
    Route::post('/companies', [CompaniesController::class, 'create'])->name('companies.create');
    Route::put('/companies/update/{id}', [CompaniesController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{id}', [CompaniesController::class, 'destroy'])->name('companies.destroy');

    // * PermintaanPembelianBarang
    Route::get('/allppb', [PermintaanPembelianBarangController::class, 'index'])->name('ppb.index');
    Route::get('/allppbumum', [PermintaanPembelianBarangController::class, 'indexppbumum'])->name('ppb.indexppbumum');
    Route::get('/allppb/draft', [PermintaanPembelianBarangController::class, 'indexDraft'])->name('ppb.indexDraft');
    Route::get('/allppb/onApproval', [PermintaanPembelianBarangController::class, 'indexOnApproval'])->name('ppb.indexOnApproval');
    Route::get('/allppb/done', [PermintaanPembelianBarangController::class, 'indexDone'])->name('ppb.indexDone');
    Route::get('/allppb/rejected', [PermintaanPembelianBarangController::class, 'indexRejected'])->name('ppb.indexRejected');
    Route::get('/ppb/{id}', [PermintaanPembelianBarangController::class, 'show'])->name('ppb.show');
    Route::post('/ppb', [PermintaanPembelianBarangController::class, 'create'])->name('ppb.create');
    Route::post('/ppb/post/{id}', [PermintaanPembelianBarangController::class, 'post'])->name('ppb.post');
    Route::put('/ppb/update/{id}', [PermintaanPembelianBarangController::class, 'update'])->name('ppb.update');
    Route::delete('/ppb/{id}', [PermintaanPembelianBarangController::class, 'destroy'])->name('ppb.destroy');
    Route::get('/ppbdashboard', [PermintaanPembelianBarangController::class, 'indexDashboard'])->name('ppb.indexDashboard');

     // * PermintaanPembelianBarangDetail
     Route::get('/allppbdetail', [PermintaanPembelianBarangDetailController::class, 'index'])->name('ppbdetail.index');
     Route::post('/ppbDetailSelect', [PermintaanPembelianBarangDetailController::class, 'ppbDetailSelect'])->name('ppbdetail.ppbDetailSelect');
     Route::get('/ppbdetaillist/{id}', [PermintaanPembelianBarangDetailController::class, 'showbyppbid'])->name('ppbdetail.showbyppbid');
     Route::get('/ppbdetail/{id}', [PermintaanPembelianBarangDetailController::class, 'show'])->name('ppbdetail.show');
     Route::post('/ppbdetail', [PermintaanPembelianBarangDetailController::class, 'create'])->name('ppbdetail.create');
     Route::put('/ppbdetail/update/{id}', [PermintaanPembelianBarangDetailController::class, 'update'])->name('ppbdetail.update');
     Route::delete('/ppbdetail/{id}', [PermintaanPembelianBarangDetailController::class, 'destroy'])->name('ppbdetail.destroy');
     Route::post('/ppbsaveAll/{ppb_id}', [PermintaanPembelianBarangDetailController::class, 'saveAll'])->name('ppbdetail.saveAll');

      // * Purchase Order
    Route::get('/allpo', [PurchaseOrderController::class, 'index'])->name('po.index');
    Route::get('/allpoumum', [PurchaseOrderController::class, 'indexpoumum'])->name('po.indexpoumum');
    Route::get('/allpodelivery', [PurchaseOrderController::class, 'indexpodelivery'])->name('po.indexpodelivery');
    Route::get('/allpo/draft', [PurchaseOrderController::class, 'indexDraft'])->name('po.indexDraft');
    Route::get('/allpo/onApproval', [PurchaseOrderController::class, 'indexOnApproval'])->name('po.indexOnApproval');
    Route::get('/allpo/done', [PurchaseOrderController::class, 'indexDone'])->name('po.indexDone');
    Route::get('/allpo/rejected', [PurchaseOrderController::class, 'indexRejected'])->name('po.indexRejected');
    Route::get('/po/{id}', [PurchaseOrderController::class, 'show'])->name('po.show');
    Route::post('/po', [PurchaseOrderController::class, 'create'])->name('po.create');
    Route::post('/po/post/{id}', [PurchaseOrderController::class, 'post'])->name('po.post');
    Route::post('/po/arrived/{id}', [PurchaseOrderController::class, 'arrived'])->name('po.arrived');
    Route::put('/po/update/{id}', [PurchaseOrderController::class, 'update'])->name('po.update');
    Route::delete('/po/{id}', [PurchaseOrderController::class, 'destroy'])->name('po.destroy');
    Route::get('/po/arrivalData/{id}', [PurchaseOrderController::class, 'arrivalData'])->name('po.arrivalData');
    Route::get('/podashboard', [PurchaseOrderController::class, 'indexDashboard'])->name('po.indexDashboard');

     // * Purchase Order Detail
     Route::get('/allpodetail', [PurchaseOrderDetailController::class, 'index'])->name('podetail.index');
     Route::get('/podetaillist/{id}', [PurchaseOrderDetailController::class, 'showbypoid'])->name('podetail.showbypoid');
     Route::get('/podetail/{id}', [PurchaseOrderDetailController::class, 'show'])->name('podetail.show');
     Route::post('/podetail', [PurchaseOrderDetailController::class, 'create'])->name('podetail.create');
     Route::put('/podetail/update/{id}', [PurchaseOrderDetailController::class, 'update'])->name('podetail.update');
     Route::delete('/podetail/{id}', [PurchaseOrderDetailController::class, 'destroy'])->name('podetail.destroy');
     Route::post('/posaveAll/{po_id}', [PurchaseOrderDetailController::class, 'saveAll'])->name('podetail.saveAll');

     // * Stock Item
    Route::get('/allstockitem', [StockItemController::class, 'index'])->name('stockitem.index');
    // Route::post('/stockitemselect', [StockItemController::class, 'index'])->name('stockitem.index');
    Route::get('/stockitem/{id}', [StockItemController::class, 'show'])->name('stockitem.show');
    Route::post('/stockiteminit/{po_detail_id}', [StockItemController::class, 'stockiteminit'])->name('stockitem.stockiteminit');
    // Route::put('/allstockitem/update/{id}', [StockItemController::class, 'update'])->name('stockitem.update');
    Route::delete('/stockitem/{id}', [StockItemController::class, 'destroy'])->name('stockitem.destroy');
    Route::post('/stockitemselect', [StockItemController::class, 'stockitemSelect'])->name('stockitem.stockitemSelect');
    Route::get('/stockitemdashboard', [StockItemController::class, 'indexDashboard'])->name('stockitem.indexDashboard');

    // * Stock Material
    Route::get('/allstockmaterial', [StockMaterialController::class, 'index'])->name('stockmaterial.index');
    Route::post('/stockmaterialselect', [StockMaterialController::class, 'index'])->name('stockmaterial.index');
    Route::get('/stockmaterial/{id}', [StockMaterialController::class, 'show'])->name('stockmaterial.show');
    Route::post('/stockmaterialinit/{po_detail_id}', [StockMaterialController::class, 'stockmaterialinit'])->name('stockmaterial.stockmaterialinit');
    Route::put('/stockmaterial/update/{id}', [StockItemController::class, 'update'])->name('stockmaterial.update');
    Route::delete('/stockmaterial/{id}', [StockMaterialController::class, 'destroy'])->name('stockmaterial.destroy');
    Route::post('/stockmaterialSelect', [StockMaterialController::class, 'stockmaterialSelect'])->name('stockmaterial.stockitemSelect');

    // * Item
    Route::get('/allitem', [ItemController::class, 'index'])->name('item.index');
    Route::post('/itemselect/{filter}', [ItemController::class, 'itemSelect'])->name('item.itemSelect');
    // Route::get('/stockitem/{id}', [StockItemController::class, 'show'])->name('stockitem.show');
    // Route::post('/stockitem', [StockItemController::class, 'create'])->name('stockitem.create');
    Route::get('/itembystock/{id}', [ItemController::class, 'showbystockid'])->name('item.showbystockid');
    Route::post('/itemsaveAll/{stock_id}/{po_detail_id}', [ItemController::class, 'saveAll'])->name('item.saveAll');
    // Route::put('/allstockitem/update/{id}', [StockItemController::class, 'update'])->name('stockitem.update');
    // Route::delete('/allstockitem/{id}', [StockItemController::class, 'destroy'])->name('stockitem.destroy');

    // * Surat Jalan
    Route::get('/allsuratjalan', [SuratJalanController::class, 'index'])->name('suratjalan.index');
    Route::get('/allsuratjalanumum', [SuratJalanController::class, 'indexsuratjalanumum'])->name('suratjalan.indexsuratjalanumum');
    Route::get('/allsuratjalan/draft', [SuratJalanController::class, 'indexDraft'])->name('suratjalan.indexDraft');
    Route::get('/allsuratjalan/onApproval', [SuratJalanController::class, 'indexOnApproval'])->name('suratjalan.indexOnApproval');
    Route::get('/allsuratjalan/done', [SuratJalanController::class, 'indexDone'])->name('suratjalan.indexDone');
    Route::get('/allsuratjalan/rejected', [SuratJalanController::class, 'indexRejected'])->name('suratjalan.indexRejected');
    Route::get('/suratjalan/{id}', [SuratJalanController::class, 'show'])->name('suratjalan.show');
    Route::post('/suratjalan', [SuratJalanController::class, 'create'])->name('suratjalan.create');
    Route::post('/suratjalan/post/{id}', [SuratJalanController::class, 'post'])->name('suratjalan.post');
    Route::put('/suratjalan/update/{id}', [SuratJalanController::class, 'update'])->name('suratjalan.update');
    Route::delete('/suratjalan/{id}', [SuratJalanController::class, 'destroy'])->name('suratjalan.destroy');
    Route::post('/suratjalanselect', [SuratJalanController::class, 'suratjalanSelect'])->name('suratjalan.suratjalanSelect');

     // * Surat Jalan Detail
     Route::get('/allsuratjalandetail', [SuratJalanDetailController::class, 'index'])->name('suratjalandetail.index');
     Route::get('/suratjalandetaillist/{id}', [SuratJalanDetailController::class, 'showbysuratjalanid'])->name('suratjalandetail.showbysuratjalanid');
     Route::post('/suratjalandetail', [SuratJalanDetailController::class, 'create'])->name('suratjalandetail.create');
     Route::delete('/suratjalandetail/{id}', [SuratJalanDetailController::class, 'destroy'])->name('suratjalandetail.destroy');
     Route::post('/suratjalansaveAll/{surat_jalan_id}', [SuratJalanDetailController::class, 'saveAll'])->name('suratjalandetail.saveAll');
     Route::get('showbyidforpengembalian/{id}', [SuratJalanDetailController::class, 'showbyidforpengembalian'])->name('suratjalandetail.showbyidforpengembalian');

      // * Surat Jalan
    Route::get('/allpengembalianbarang', [PengembalianBarangController::class, 'index'])->name('pengembalianbarang.index');
    // Route::get('/allpengembalianbarangumum', [PengembalianBarangController::class, 'indexpengembalianbarangumum'])->name('pengembalianbarang.indexpengembalianbarangumum');
    Route::get('/allpengembalianbarang/draft', [PengembalianBarangController::class, 'indexDraft'])->name('pengembalianbarang.indexDraft');
    Route::get('/allpengembalianbarang/onApproval', [PengembalianBarangController::class, 'indexOnApproval'])->name('pengembalianbarang.indexOnApproval');
    Route::get('/allpengembalianbarang/done', [PengembalianBarangController::class, 'indexDone'])->name('pengembalianbarang.indexDone');
    Route::get('/allpengembalianbarang/rejected', [PengembalianBarangController::class, 'indexRejected'])->name('pengembalianbarang.indexRejected');
    Route::get('/pengembalianbarang/{id}', [PengembalianBarangController::class, 'show'])->name('pengembalianbarang.show');
    Route::post('/pengembalianbarang', [PengembalianBarangController::class, 'create'])->name('pengembalianbarang.create');
    Route::post('/pengembalianbarang/post/{id}', [PengembalianBarangController::class, 'post'])->name('pengembalianbarang.post');
    Route::put('/pengembalianbarang/update/{id}', [PengembalianBarangController::class, 'update'])->name('pengembalianbarang.update');
    Route::delete('/pengembalianbarang/{id}', [PengembalianBarangController::class, 'destroy'])->name('pengembalianbarang.destroy');

     // * Surat Jalan Detail
     Route::get('/allpengembalianbarangdetail', [PengembalianBarangDetailController::class, 'index'])->name('pengembalianbarangdetail.index');
     Route::get('/pengembalianbarangdetaillist/{id}', [PengembalianBarangDetailController::class, 'showbyid'])->name('pengembalianbarangdetail.showbyid');
    //  Route::post('/pengembalianbarangdetail', [PengembalianBarangDetailController::class, 'create'])->name('pengembalianbarangdetail.create');
     Route::delete('/pengembalianbarangdetail/{id}', [PengembalianBarangDetailController::class, 'destroy'])->name('pengembalianbarangdetail.destroy');
     Route::post('/pengembalianbarangsaveAll/{surat_jalan_id}', [PengembalianBarangDetailController::class, 'saveAll'])->name('pengembalianbarangdetail.saveAll');

      // * BuktiPengeluaranBarang
    Route::get('/allbpb', [BuktiPengeluaranBarangController::class, 'index'])->name('bpb.index');
    Route::get('/allbpbumum', [BuktiPengeluaranBarangController::class, 'indexbpbumum'])->name('bpb.indexbpbumum');
    Route::get('/allbpbdelivery', [BuktiPengeluaranBarangController::class, 'indexbpbdelivery'])->name('bpb.indexbpbdelivery');
    Route::get('/allbpb/draft', [BuktiPengeluaranBarangController::class, 'indexDraft'])->name('bpb.indexDraft');
    Route::get('/allbpb/onApproval', [BuktiPengeluaranBarangController::class, 'indexOnApproval'])->name('bpb.indexOnApproval');
    Route::get('/allbpb/done', [BuktiPengeluaranBarangController::class, 'indexDone'])->name('bpb.indexDone');
    Route::get('/allbpb/rejected', [BuktiPengeluaranBarangController::class, 'indexRejected'])->name('bpb.indexRejected');
    Route::get('/bpb/{id}', [BuktiPengeluaranBarangController::class, 'show'])->name('bpb.show');
    Route::post('/bpb', [BuktiPengeluaranBarangController::class, 'create'])->name('bpb.create');
    Route::post('/bpb/post/{id}', [BuktiPengeluaranBarangController::class, 'post'])->name('bpb.post');
    Route::post('/bpb/postToOutsanding/{id}', [BuktiPengeluaranBarangController::class, 'postToOutstanding'])->name('bpb.postToOutstanding');
    Route::put('/bpb/update/{id}', [BuktiPengeluaranBarangController::class, 'update'])->name('bpb.update');
    Route::delete('/bpb/{id}', [BuktiPengeluaranBarangController::class, 'destroy'])->name('bpb.destroy');

     // * BuktiPengeluaranBarangDetail
    Route::get('/allbpbdetail', [BuktiPengeluaranBarangDetailController::class, 'index'])->name('bpbdetail.index');
    Route::post('/bpbDetailSelect', [BuktiPengeluaranBarangDetailController::class, 'bpbDetailSelect'])->name('bpbdetail.bpbDetailSelect');
    Route::get('/bpbdetaillist/{id}', [BuktiPengeluaranBarangDetailController::class, 'showbybpbid'])->name('bpbdetail.showbybpbid');
    Route::get('/bpbdetail/{id}', [BuktiPengeluaranBarangDetailController::class, 'show'])->name('bpbdetail.show');
    Route::post('/bpbdetail', [BuktiPengeluaranBarangDetailController::class, 'create'])->name('bpbdetail.create');
    Route::put('/bpbdetail/update/{id}', [BuktiPengeluaranBarangDetailController::class, 'update'])->name('bpbdetail.update');
    Route::delete('/bpbdetail/{id}', [BuktiPengeluaranBarangDetailController::class, 'destroy'])->name('bpbdetail.destroy');
    Route::post('/bpbdetailsaveAll/{bpb_id}', [BuktiPengeluaranBarangDetailController::class, 'saveAll'])->name('bpbdetail.saveAll');
    Route::post('/bpbdetail/deliver/{id}', [BuktiPengeluaranBarangDetailController::class, 'deliver'])->name('bpbdetail.deliver');

     // * BuktiPengeluaranBarangDetailDetail
     Route::get('/allbpbdetaildetail', [BuktiPengeluaranBarangDetailDetailController::class, 'index'])->name('bpbdetaildetail.index');
     Route::post('/bpbDetailDetailSelect', [BuktiPengeluaranBarangDetailDetailController::class, 'bpbDetailSelect'])->name('bpbdetaildetail.bpbDetailSelect');
     Route::get('/bpbdetaildetaillist/{id}', [BuktiPengeluaranBarangDetailDetailController::class, 'showbybpbid'])->name('bpbdetaildetail.showbybpbid');
     Route::get('/bpbdetaildetail/{id}', [BuktiPengeluaranBarangDetailDetailController::class, 'show'])->name('bpbdetaildetail.show');
     Route::post('/bpbdetaildetail', [BuktiPengeluaranBarangDetailDetailController::class, 'create'])->name('bpbdetaildetail.create');
     Route::put('/bpbdetaildetail/update/{id}', [BuktiPengeluaranBarangDetailDetailController::class, 'update'])->name('bpbdetaildetail.update');
     Route::delete('/bpbdetaildetail/{id}', [BuktiPengeluaranBarangDetailDetailController::class, 'destroy'])->name('bpbdetaildetail.destroy');
     Route::post('/bpbdetaildetailsaveAll/{bpb_id}', [BuktiPengeluaranBarangDetailDetailController::class, 'saveAll'])->name('bpbdetaildetail.saveAll');
     Route::post('/bpbdetail/deliver/{id}', [BuktiPengeluaranBarangDetailDetailController::class, 'deliver'])->name('bpbdetaildetail.deliver');

    // * Approval
    Route::get('/allindexApprovalRecord', [ApprovalController::class, 'indexRecord'])->name('indexRecord');
    Route::get('/allindexApproval', [ApprovalController::class, 'indexApproval'])->name('indexApproval');
    Route::post('/approval/approve', [ApprovalController::class, 'approve'])->name('approve');
    Route::post('/approval/reject', [ApprovalController::class, 'reject'])->name('reject');
    Route::post('/approval/return', [ApprovalController::class, 'return'])->name('return');

});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
