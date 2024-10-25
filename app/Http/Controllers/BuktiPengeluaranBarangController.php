<?php

namespace App\Http\Controllers;

use App\Models\BuktiPengeluaranBarang;
use App\Models\BuktiPengeluaranBarangDetail;
use App\Models\BuktiPengeluaranBarangDetailDetail;
use App\Models\StockItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuktiPengeluaranBarangController extends Controller
{
    public function index()
    {
        $bpb = BuktiPengeluaranBarang::orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function indexbpbumum()
    {
        $bpb = BuktiPengeluaranBarang::orderBy('date')->where('status','!=', 'Draft')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function indexbpbdelivery()
    {

        $userRole = Auth::user()->role;
        if ($userRole == 'INVENTORY') {
            $bpb = BuktiPengeluaranBarang::orderBy('date')->where('status', 'Awaiting Warehouse Confirmation')->get();
        } else {
            $bpb = [];
        }

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function indexDraft()
    {
        $user_id = Auth::user()->id;
        $bpb = BuktiPengeluaranBarang::where('request_by_id' , $user_id)
                                        ->where('status', 'Draft')
                                        ->orWhere('status', 'Returned')
                                        ->orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }
    public function indexOnApproval()
    {
        $user_id = Auth::user()->id;
        $bpb = BuktiPengeluaranBarang::where('request_by_id' , $user_id)->where('status', 'On Approval')->orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }
    public function indexDone()
    {
        $user_id = Auth::user()->id;
        $bpb = BuktiPengeluaranBarang::where('request_by_id' , $user_id)->where('status', 'Done')->orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function indexRejected()
    {
        $user_id = Auth::user()->id;
        $bpb = BuktiPengeluaranBarang::where('request_by_id' , $user_id)->where('status', 'Rejected')->orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function show($id)
    {
        $bpb = BuktiPengeluaranBarang::find($id);

        if ($bpb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang not found!',
                'data' => $bpb
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang retrieved successfully!',
                'data' => $bpb
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {

        $bpb = BuktiPengeluaranBarang::find($id);

        if ($bpb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang not found!',
                'data' => $bpb
            ], 404);
        } else {
            $bpb->update([
                'salesman'=>$request->salesman,
                'no_po'=>$request->no_po,
                'delivery_by'=>$request->delivery_by,
                'delivery_date'=>$request->delivery_date,
                'is_partial_delivery'=>$request->is_partial_delivery,
                'customer'=>$request->customer,
                'customer_address'=>$request->customer_address,
                'customer_pic_name'=>$request->customer_pic_name,
                'customer_pic_phone'=>$request->customer_pic_phone,
                'approved_by'=>$request->approved_by,
                'approved_by_id'=>$request->approved_by_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang updated successfully!',
                'data' => $bpb
            ], 200);
        }
    }

    public function destroy($id)
    {
        $bpb = BuktiPengeluaranBarang::find($id);

        if ($bpb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang not found!',
                'data' => $bpb
            ], 404);
        } else {
            $bpb->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang deleted successfully!',
            ], 200);
        }
    }

    public function create( Request $request)
    {
        $no_bpb = $this->generateNoBpb();

        $request_by = Auth::user()->name;
        $request_by_id = Auth::user()->id;

        $formattedDate = Carbon::now()->format('Y-m-d');

        $bpb = BuktiPengeluaranBarang::create([
            'no_bpb'=>$no_bpb,
            'status'=>'Draft',
            'delivery_status' => null,
            'salesman'=>$request->salesman,
            'date'=>$formattedDate,
            'no_po'=>$request->no_po,
            'delivery_by'=>$request->delivery_by,
            'delivery_date'=>$request->delivery_date,
            'is_partial_delivery'=>$request->is_partial_delivery,
            'customer'=>$request->customer,
            'customer_address'=>$request->customer_address,
            'customer_pic_name'=>$request->customer_pic_name,
            'customer_pic_phone'=>$request->customer_pic_phone,
            'request_by'=>$request_by,
            'request_by_id'=>$request_by_id,
            'request_by_date'=>$formattedDate,
            'approved_by' => $request->approved_by,
            'approved_by_id' => $request->approved_by_id,
            'approved_by_date' => null,
            'approved_by_status' => null,
            'remarks' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan Pembelian Barang successfully created!',
            'data' => $bpb,
        ], 200);
    }

    private function generateNoBpb()
    {
    return DB::transaction(function () {
        $currentDate = Carbon::now();
        $year = $currentDate->year;
        $month = str_pad($currentDate->month, 2, '0', STR_PAD_LEFT);

        // Cari nomor terbesar yang ada saat ini
        $lastNoBpb = BuktiPengeluaranBarang::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('no_bpb', 'desc')
            ->value('no_bpb');

        // Jika ada nomor sebelumnya, increment nomor tersebut, jika tidak, mulai dari 1
        if ($lastNoBpb) {
            $lastSequence = (int) substr($lastNoBpb, -3);
            $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '001';
        }

        $newNoBpb = 'bpb_'.$year . $month . $newSequence;

        return $newNoBpb;
    });
    }

    // public function post($id)
    // {

    //     $bpb = BuktiPengeluaranBarang::find($id);
    //     $bpb_detail = BuktiPengeluaranBarangDetail::where('bpb_id', $id)->get();

    //     if ($bpb == null) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Bukti Pengeluaran Barang not found!',
    //             'data' => $bpb
    //         ], 404);
    //     }

    //     if (count($bpb_detail) == 0) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Item Bukti Pengeluaran Barang minimal 1 item!',
    //             'data' => $bpb_detail
    //         ], 404);
    //     }

    //     if( $bpb->salesman == null || $bpb->no_po == null || $bpb->delivery_by == null || $bpb->delivery_date == null || $bpb->is_partial_delivery == null  || $bpb->customer == null ||
    //         $bpb->customer_address == null || $bpb->customer_pic_name == null || $bpb->customer_pic_phone == null || $bpb->approved_by == null){
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Tidak boleh ada data Bukti Pengeluaran Barang yang kosong'
    //         ], 400);
        
    //     }

    //     foreach($bpb_detail as $item){
    //         if($item->bpb_id == null || $item->stock_name == null || $item->quantity == null){
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Tidak boleh ada data Item yang kosong'
    //             ], 400);
    //         }
    //     }

    //     foreach($bpb_detail as $item){
    //         for ($i = 0; $i < $item->quantity; $i++) {
    //             BuktiPengeluaranBarangDetailDetail::create([
    //                 'ppb_id'=>$item->ppb_id,
    //                 'ppb_detail_id'=>$item->id,
    //             ]);
    //         }
    //     }
    
    //     $bpb->update([
    //         'status'=>'On Approval',
    //         'approved_by_status'=>'Waiting for Confirmation',
    //         'approved_by_date'=>null,
    //         'remarks'=>null,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Bukti Pengeluaran Barang updated successfully!',
    //         'data' => $bpb
    //     ], 200);
    // }

    public function post($id)
    {

        $bpb = BuktiPengeluaranBarang::find($id);
        $bpb_detail = BuktiPengeluaranBarangDetail::where('bpb_id', $id)->get();

        if ($bpb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang not found!',
                'data' => $bpb
            ], 404);
        }

        if (count($bpb_detail) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Item Bukti Pengeluaran Barang minimal 1 item!',
                'data' => $bpb_detail
            ], 404);
        }

        if( $bpb->salesman == null || $bpb->no_po == null || $bpb->delivery_by == null || $bpb->delivery_date == null || $bpb->customer == null ||
            $bpb->customer_address == null || $bpb->customer_pic_name == null || $bpb->customer_pic_phone == null || $bpb->approved_by == null){
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh ada data Bukti Pengeluaran Barang yang kosong',
                'data' => $bpb,
            ], 400);
        
        }

        foreach($bpb_detail as $item){
            if($item->bpb_id == null || $item->stock_name == null || $item->quantity == null){
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak boleh ada data Item yang kosong'
                ], 400);
            }
            
            $stock = StockItem::find($item->stock_id);
            if($stock->quantity < $item->quantity){    
                return response()->json([
                    'success' => false,
                    'message' => 'Stok item '.$stock->stock_name.' di gudang hanya tersedia '.$stock->quantity,
                    'data' => $stock,
                ], 400);
            }
        }

        foreach($bpb_detail as $item){
            for ($i = 0; $i < $item->quantity; $i++) {
                BuktiPengeluaranBarangDetailDetail::create([
                    'bpb_id'=>$item->bpb_id,
                    'bpb_detail_id'=>$item->id,
                ]);
            }
        }
    
        $bpb->update([
            'status'=>'On Approval',
            'approved_by_status'=>'Waiting for Confirmation',
            'approved_by_date'=>null,
            'remarks'=>null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti Pengeluaran Barang updated successfully!',
            'data' => $bpb
        ], 200);
    }

}
