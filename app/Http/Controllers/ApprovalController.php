<?php

namespace App\Http\Controllers;

use App\Models\PermintaanPembelianBarang;
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
                                                $item->tipe = 'ppb'; // Gantilah 'some_value' dengan nilai yang sesuai
                                                return $item;
                                            });;

        return response()->json([
            'success' => true,
            'message' => 'Data Approval successfully retrieved!',
            'data' => $ppb
        ], 200);
    }

    public function approve(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
        }

        if ($ppb != null and $request->tipe == 'ppb') {
            if($ppb->mengetahui_id == $ppb->menyetujui_id){
                $ppb->update([
                    'mengetahui_status' => 'Approved',
                    'menyetujui_status' => 'Approved',
                    'status' => 'Approved',
                ]);
            }elseif($ppb->mengetahui_status == 'Waiting for Confirmation'){
                $ppb->update([
                    'mengetahui_status' => 'Approved',
                ]);
            }else{
                $ppb->update([
                    'menyetujui_status' => 'Approved',
                    'status' => 'Approved',
                ]);
            }
        }
    }

    public function reject(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
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
    }
    
    public function return(Request $request){

        if($request->tipe == 'ppb'){
            $ppb = PermintaanPembelianBarang::find($request->id);
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

    }
}
