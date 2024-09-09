<?php

namespace App\Http\Controllers;

use App\Models\PermintaanPembelianBarang;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function indexApproval(){
        $currentUserId = auth()->id();

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

                                            
        // Menggabungkan kedua koleksi
        $mergedData = $ppb->merge($po);

        return response()->json([
            'success' => true,
            'message' => 'Data Approval successfully retrieved!',
            'data' => $mergedData
        ], 200);
    }

    public function approve(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
            $po = null;
        }else if($request->tipe == 'po'){
            $po = PurchaseOrder::find($request->id);
            $ppb = null;
        }

        if ($ppb !== null && $request->tipe == 'ppb') {
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
            }elseif($po->verified_by_id == 'Waiting for Confirmation'){
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
        }
    }

    public function reject(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
            $po = null;
        }else if($request->tipe == 'po'){
            $po = PurchaseOrder::find($request->id);
            $ppb = null;
        }

        if ($ppb != null and $request->tipe == 'ppb') {
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
        }

        if ($po != null and $request->tipe == 'po') {

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
        }
    }
    
    public function return(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
            $po = null;
        }else if($request->tipe == 'po'){
            $po = PurchaseOrder::find($request->id);
            $ppb = null;
        }    

        if ($ppb != null and $request->tipe == 'ppb') {
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
            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $ppb
            ],200);
        }

        if ($po != null and $request->tipe == 'po') {

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
            return response()->json([
                'success' => true,
                'message' => 'Data Approval successfully retrieved!',
                'data' => $po
            ],200);
        }

    }
}
