<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRecord;
use App\Models\BuktiPengeluaranBarang;
use App\Models\BuktiPengeluaranBarangDetail;
use App\Models\BuktiPengeluaranBarangDetailDetail;
use App\Models\Item;
use App\Models\PermintaanPembelianBarang;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\StockItem;
use App\Models\StockMaterial;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function indexApproval(){
        $currentUserId = Auth::user()->id;

        $ppb = PermintaanPembelianBarang::where('mengetahui_id', $currentUserId)->where('mengetahui_status', 'Waiting for Confirmation')
                                            ->orWhere('menyetujui_id', $currentUserId)->where('mengetahui_status', 'Approved')
                                            ->where('menyetujui_status', 'Waiting for Confirmation')
                                            ->get()
                                            ->map(function($item) {
                                                $item->tipe = 'ppb';
                                                $item->no = $item->no_ppb; // Gantilah 'some_value' dengan nilai yang sesuai
                                                return $item;
                                            });;

        $po = PurchaseOrder::where('verified_by_id', $currentUserId)->where('verified_by_status', 'Waiting for Confirmation')
                                            ->orWhere('approved_by_id', $currentUserId)->where('verified_by_status', 'Approved')
                                            ->where('approved_by_status', 'Waiting for Confirmation')
                                            ->get()
                                            ->map(function($item) {
                                                $item->tipe = 'po';
                                                $item->no = $item->no_po;
                                                $item->pemohon = $item->prepared_by; // Gantilah 'some_value' dengan nilai yang sesuai
                                                return $item;
                                            });;                                    
                    
        $bpb = BuktiPengeluaranBarang::where('approved_by_id', $currentUserId)->where('approved_by_status', 'Waiting for Confirmation')
                                                ->get()
                                                ->map(function($item) {
                                                $item->tipe = 'bpb';
                                                $item->no = $item->no_bpb;
                                                $item->tanggal  = $item->date;
                                                $item->pemohon = $item->request_by; // Gantilah 'some_value' dengan nilai yang sesuai
                                                return $item;
                                            });;   
        $surat_jalan = SuratJalan::where('mengetahui_id', $currentUserId)->where('mengetahui_status', 'Waiting for Confirmation')
                                            ->get()
                                            ->map(function($item) {
                                            $item->tipe = 'Surat Jalan';
                                            $item->no = $item->no_surat_jalan;
                                            $item->tanggal  = $item->menyerahkan_date;
                                            $item->pemohon = $item->menyerahkan; // Gantilah 'some_value' dengan nilai yang sesuai
                                            return $item;
                                        });;                                               
                                            
        // Menggabungkan kedua koleksi
        $mergedData = $ppb->merge($po);
        $mergedData = $mergedData->merge($bpb);
        $mergedData = $mergedData->merge($surat_jalan);

        return response()->json([
            'success' => true,
            'message' => 'Data Approval successfully retrieved!',
            'data' => $mergedData
        ], 200);
    }

    public function indexRecord(){

        $approvalRecord = ApprovalRecord::orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Approval Record successfully retrieved!',
            'data' => $approvalRecord
        ], 200);
    } 

    private function createRecord($no, $date, $type, $requestor, $requestor_id, $approver, $approver_id, $action, $remarks)
    {
        $approvalRecord = ApprovalRecord::create([
            'no' => $no,
            'date' => $date,
            'type' => $type,
            'requestor' => $requestor,
            'requestor_id' => $requestor_id,
            'approver' => $approver,
            'approver_id' => $approver_id,
            'action' => $action,
            'remarks' => $remarks,

        ]);

        return response()->json([
            'success' => true,
            'message' => 'Prinsipal successfully created!',
            'data' => $approvalRecord
        ], 200);
        

        return $approvalRecord;
    }

    private function deliver($id){
        DB::beginTransaction();
        try {

            $bpb_detail_detail = BuktiPengeluaranBarangDetailDetail::where('bpb_id', $id)->get();

            if ($bpb_detail_detail == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukti Pengeluaran Barang Detail not found!',
                    'data' => $bpb_detail_detail
                ], 404);
            }

            foreach ($bpb_detail_detail as $detail) {
                
                $item = Item::find($detail->item_id);
        
                if($item == null){
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak terdapat Item pada gudang!',
                        'data' => $item
                    ], 400);
                }
        
                $stockitem = StockItem::where('id', $item->stock_id)->first();

                $formattedDate = Carbon::now()->format('Y-m-d');
        
                $item->update([
                    'is_in_stock' => 0,
                    'leaving_date' => $formattedDate
                ]);
        
                $stockitem->update([
                    'quantity' => $stockitem->quantity - 1
                ]);
        
            }

            DB::commit();

            return $bpb_detail_detail;

        } catch (\Exception $e) {

            // Jika terjadi kesalahan, rollback semua perubahan
            DB::rollBack();
    
            return response()->json(['error' => $e->getMessage()], 400);
        }
        
    }

    private function deliverSuratJalan($id){
        $surat_jalan_detail = SuratJalanDetail::where('surat_jalan_id', $id)->get();

        if ($surat_jalan_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Surat Jalan Detail not found!',
                'data' => $surat_jalan_detail
            ], 404);
        }

        foreach ($surat_jalan_detail as $detail) {
            
            $stockmaterial = StockMaterial::find($detail->stock_material_id);
    
            if($stockmaterial == null){
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak terdapat Item pada gudang!',
                    'data' => $stockmaterial
                ], 400);
            }

            $stockmaterial->update([
                'quantity' => $stockmaterial->quantity - $detail->quantity
            ]);
    
        }
        return $surat_jalan_detail;
        
    }

    public function approve(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
            $po = null;
            $bpb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'po'){
            $po = PurchaseOrder::find($request->id);
            $ppb = null;
            $bpb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'bpb'){
            $bpb = BuktiPengeluaranBarang::find($request->id);
            $po = null;
            $ppb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'Surat Jalan'){
            $surat_jalan = SuratJalan::find($request->id);
            $ppb = null;
            $po = null;
            $bpb = null;
        }

        $user = Auth::user();

        if ($ppb !== null && $request->tipe == 'ppb') {
            $formattedDate = Carbon::now()->format('Y-m-d');

            if($ppb->mengetahui_id == $ppb->menyetujui_id){
                $ppb->update([
                    'mengetahui_status' => 'Approved',
                    'menyetujui_status' => 'Approved',
                    'status' => 'Done',
                ]);
            }elseif($ppb->mengetahui_status == 'Waiting for Confirmation'){
                $ppb->update([
                    'mengetahui_status' => 'Approved',
                ]);
            }else{
                $ppb->update([
                    'menyetujui_status' => 'Approved',
                    'status' => 'Done',
                ]);
            }

            $record = $this->createRecord($ppb->no_ppb, $formattedDate, 'ppb', $ppb->pemohon, $ppb->pemohon_id, $user->name, $user->id, 'Approved', $request->remarks);
            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $ppb,
                'data2' => $record
            ],200);
        }

        if ($po !== null && $request->tipe == 'po') {

            $po_detail = PurchaseOrderDetail::where('po_id', $request->id)->get();
            
            $formattedDate = Carbon::now()->format('Y-m-d');

            if($po->verified_by_id == $po->approved_by_id){
                $po->update([
                    'verified_by_status' => 'Approved',
                    'verified_by_date' => $formattedDate,
                    'approved_by_status' => 'Approved',
                    'approved_by_date' => $formattedDate,
                    'status' => 'Done',
                    'arrival_status' => 'Awaiting Delivery',
                ]);

                foreach ($po_detail as $detail) {
                    $ppb = PermintaanPembelianBarang::where('no_ppb', $detail->no_ppb);
                    $ppb->update([
                        'purchasing' => $po->prepared_by,
                        'purchasing_id' => $po->prepared_by_id,
                        'purchasing_status' => 'Approved',
                    ]);
                }

            }elseif($po->verified_by_status == 'Waiting for Confirmation'){
                $po->update([
                    'verified_by_status' => 'Approved',
                    'verified_by_date' => $formattedDate,
                ]);
            }else{
                $po->update([
                    'approved_by_status' => 'Approved',
                    'approved_by_date' => $formattedDate,
                    'status' => 'Done',
                    'arrival_status' => 'Awaiting Delivery',
                ]);

                foreach ($po_detail as $detail) {
                    $ppb = PermintaanPembelianBarang::where('no_ppb', $detail->no_ppb);
                    $ppb->update([
                        'purchasing' => $po->prepared_by,
                        'purchasing_id' => $po->prepared_by_id,
                        'purchasing_status' => 'Approved',
                    ]);
                }
            }

            $record = $this->createRecord($po->no_po, $formattedDate, 'po', $po->prepared_by, $po->prepared_by_id, $user->name, $user->id, 'Approved', $request->remarks);
            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $po,
                'data2' => $record
            ],200);
        }

        if ($bpb !== null && $request->tipe == 'bpb') {

            $bpb_detail_detail = BuktiPengeluaranBarangDetailDetail::where('bpb_id', $request->id)->where('item_id', null)->get();

            if($bpb_detail_detail->count() > 0){
                return response()->json([
                    'success' => false,
                    'message' => 'Lengkapi data Item sebelum melanjutkan approval!',
                    'data' => $bpb_detail_detail
                ],400);
            };
            
            $formattedDate = Carbon::now()->format('Y-m-d');

            $bpb->update([
                'approved_by_status' => 'Approved',
                'approved_by_date' => $formattedDate,
                'status' => 'Done',
                'delivery_status' => 'Awaiting Delivery',
            ]);

            $record = $this->createRecord($bpb->no_bpb, $formattedDate, 'bpb', $bpb->request_by, $bpb->request_by_id, $user->name, $user->id, 'Approved', $request->remarks);
            
            $this->deliver($request->id);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $bpb,
                'data2' => $bpb_detail_detail,
                'data3' => $record
            ],200);
        }

        if ($surat_jalan !== null && $request->tipe == 'Surat Jalan') {
            
            $formattedDate = Carbon::now()->format('Y-m-d');

            $surat_jalan->update([
                'mengetahui_status' => 'Approved',
                'mengetahui_date' => $formattedDate,
                'status' => 'Done',
            ]);

            $record = $this->createRecord($surat_jalan->no_surat_jalan, $formattedDate, 'Surat Jalan', $surat_jalan->menyerahkan, $surat_jalan->menyerahkan_id, $user->name, $user->id, 'Approved', $request->remarks);
            
            $this->deliverSuratJalan($request->id);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $surat_jalan,
                'data2' => $record
            ],200);
        }
    }

    public function reject(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
            $po = null;
            $bpb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'po'){
            $po = PurchaseOrder::find($request->id);
            $ppb = null;
            $bpb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'bpb'){
            $bpb = BuktiPengeluaranBarang::find($request->id);
            $po = null;
            $ppb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'Surat Jalan'){
            $surat_jalan = SuratJalan::find($request->id);
            $ppb = null;
            $po = null;
            $bpb = null;
        }

        $user = Auth::user();

        if ($ppb != null && $request->tipe == 'ppb') {
            $formattedDate = Carbon::now()->format('Y-m-d');
            if($ppb->mengetahui_id == $ppb->menyetujui_id){
                $ppb->update([
                    'mengetahui_status' => 'Rejected',
                    'menyetujui_status' => 'Rejected',
                    'status' => 'Rejected',
                    'remarks' => $request->remarks ."\n". 'Rejected by : ' . Auth::user()->name
                ]);
            }elseif($ppb->mengetahui_status == 'Waiting for Confirmation'){
                $ppb->update([
                    'mengetahui_status' => 'Rejected',
                    'menyetujui_status' => '-',
                    'status' => 'Rejected',
                    'remarks' => $request->remarks ."\n". 'Rejected by : ' . Auth::user()->name
                ]);
            }else{
                $ppb->update([
                    'menyetujui_status' => 'Rejected',
                    'status' => 'Rejected',
                    'remarks' => $request->remarks . "\n". 'Rejected by : ' . Auth::user()->name
                ]);
            }
            $record = $this->createRecord($ppb->no_ppb, $formattedDate, 'ppb', $ppb->pemohon, $ppb->pemohon_id, $user->name, $user->id, 'Rejected', $request->remarks);
            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $ppb,
                'data2' => $record
            ],200);
        }

        if ($po != null && $request->tipe == 'po') {

            $formattedDate = Carbon::now()->format('Y-m-d');

            if($po->verified_by_id == $po->approved_by_id){
                $po->update([
                    'verified_by_status' => 'Rejected',
                    'verified_by_date' => $formattedDate,
                    'approved_by_status' => 'Rejected',
                    'approved_by_date' => $formattedDate,
                    'status' => 'Rejected',
                    'remarks' => $request->remarks ."\n". 'Rejected by : ' . Auth::user()->name
                ]);
            }elseif($po->verified_by_id == 'Waiting for Confirmation'){
                $po->update([
                    'verified_by_status' => 'Rejected',
                    'verified_by_date' => $formattedDate,
                    'approved_by_status' => '-',
                    'status' => 'Rejected',
                    'remarks' => $request->remarks ."\n". 'Rejected by : ' . Auth::user()->name
                ]);
            }else{
                $po->update([
                    'approved_by_status' => 'Rejected',
                    'approved_by_date' => $formattedDate,
                    'status' => 'Rejected',
                    'remarks' => $request->remarks . "\n". 'Rejected by : ' . Auth::user()->name
                ]);
            }

            $record = $this->createRecord($po->no_po, $formattedDate, 'po', $po->prepared_by, $po->prepared_by_id, $user->name, $user->id, 'Rejected', $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $po,
                'data2' => $record
            ],200);
        }

        if ($bpb != null && $request->tipe == 'bpb') {

            $formattedDate = Carbon::now()->format('Y-m-d');

            $bpb->update([
                'approved_by_status' => 'Rejected',
                'approved_by_date' => $formattedDate,
                'status' => 'Rejected',
                'remarks' => $request->remarks . "\n". 'Rejected by : ' . Auth::user()->name
            ]);
            
            $record = $this->createRecord($bpb->no_bpb, $formattedDate, 'bpb', $bpb->request_by, $bpb->request_by_id, $user->name, $user->id, 'Rejected', $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $bpb,
                'data2' => $record
            ],200);
        }

        if ($surat_jalan != null && $request->tipe == 'Surat Jalan') {

            $formattedDate = Carbon::now()->format('Y-m-d');

            $surat_jalan->update([
                'mengetahui_status' => 'Rejected',
                'mengetahui_date' => $formattedDate,
                'status' => 'Rejected',
                'remarks' => $request->remarks . "\n". 'Rejected by : ' . Auth::user()->name
            ]);
            
            $record = $this->createRecord($surat_jalan->no_surat_jalan, $formattedDate, 'Surat Jalan', $surat_jalan->menyerahkan, $surat_jalan->menyerahkan_id, $user->name, $user->id, 'Rejected', $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $surat_jalan,
                'data2' => $record
            ],200);
        }
    }
    
    public function return(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
            $po = null;
            $bpb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'po'){
            $po = PurchaseOrder::find($request->id);
            $ppb = null;
            $bpb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'bpb'){
            $bpb = BuktiPengeluaranBarang::find($request->id);
            $po = null;
            $ppb = null;
            $surat_jalan = null;
        }else if($request->tipe == 'Surat Jalan'){
            $surat_jalan = SuratJalan::find($request->id);
            $ppb = null;
            $po = null;
            $bpb = null;
        }

        $user = Auth::user();

        if ($ppb != null && $request->tipe == 'ppb') {
            $formattedDate = Carbon::now()->format('Y-m-d');
            if($ppb->mengetahui_id == $ppb->menyetujui_id){
                $ppb->update([
                    'mengetahui_status' => 'Returned',
                    'menyetujui_status' => 'Returned',
                    'status' => 'Returned',
                    'remarks' => $request->remarks ."\n". 'Returned by : ' . Auth::user()->name
                ]);
            }elseif($ppb->mengetahui_status == 'Waiting for Confirmation'){
                $ppb->update([
                    'mengetahui_status' => 'Returned',
                    'menyetujui_status' => '-',
                    'status' => 'Returned',
                    'remarks' => $request->remarks ."\n". 'Returned by : ' . Auth::user()->name
                ]);
            }else{
                $ppb->update([
                    'menyetujui_status' => 'Returned',
                    'status' => 'Returned',
                    'remarks' => $request->remarks ."\n". 'Returned by : ' . Auth::user()->name
                ]);
            }

            $record = $this->createRecord($ppb->no_ppb, $formattedDate, 'ppb', $ppb->pemohon, $ppb->pemohon_id, $user->name, $user->id, 'Returned', $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $ppb,
                'data2' => $record
            ],200);
        }

        if ($po != null && $request->tipe == 'po') {

            $formattedDate = Carbon::now()->format('Y-m-d');

            if($po->verified_by_id == $po->approved_by_id){
                $po->update([
                    'verified_by_status' => 'Returned',
                    'verified_by_date' => $formattedDate,
                    'approved_by_status' => 'Returned',
                    'approved_by_date' => $formattedDate,
                    'status' => 'Returned',
                    'remarks' => $request->remarks ."\n". 'Returned by : ' . Auth::user()->name
                ]);
            }elseif($po->verified_by_id == 'Waiting for Confirmation'){
                $po->update([
                    'verified_by_status' => 'Returned',
                    'verified_by_date' => $formattedDate,
                    'approved_by_status' => '-',
                    'status' => 'Returned',
                    'remarks' => $request->remarks ."\n". 'Returned by : ' . Auth::user()->name
                ]);
            }else{
                $po->update([
                    'approved_by_status' => 'Returned',
                    'approved_by_date' => $formattedDate,
                    'status' => 'Returned',
                    'remarks' => $request->remarks ."\n". 'Returned by : ' . Auth::user()->name
                ]);
            }

            $record = $this->createRecord($po->no_po, $formattedDate, 'po', $po->prepared_by, $po->prepared_by_id, $user->name, $user->id, 'Returned', $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $po,
                'data2' => $record
            ],200);
        }

        if ($bpb != null && $request->tipe == 'bpb') {

            $formattedDate = Carbon::now()->format('Y-m-d');

            $bpb->update([
                'approved_by_status' => 'Returned',
                'approved_by_date' => $formattedDate,
                'status' => 'Returned',
                'remarks' => $request->remarks ."\n". 'Returned by : ' . Auth::user()->name
            ]);

            $bpb_detail_detail = BuktiPengeluaranBarangDetailDetail::where('bpb_id', $bpb->id)->get();
            foreach ($bpb_detail_detail as $bpb_detail_detail) {
                $bpb_detail_detail->delete();
            }
            
            $record = $this->createRecord($bpb->no_bpb, $formattedDate, 'bpb', $bpb->request_by, $bpb->request_by_id, $user->name, $user->id, 'Returned', $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $bpb,
                'data2' => $record
            ],200);
        }

        if ($surat_jalan != null && $request->tipe == 'Surat Jalan') {

            $formattedDate = Carbon::now()->format('Y-m-d');

            $surat_jalan->update([
                'mengetahui_status' => 'Returned',
                'mengetahui_date' => $formattedDate,
                'status' => 'Returned',
                'remarks' => $request->remarks ."\n". 'Returned by : ' . Auth::user()->name
            ]);
            
            $record = $this->createRecord($surat_jalan->no_surat_jalan, $formattedDate, 'Surat Jalan', $surat_jalan->menyerahkan, $surat_jalan->menyerahkan_id, $user->name, $user->id, 'Returned', $request->remarks);

            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $surat_jalan,
                'data2' => $record
            ],200);
        }

    }
}
