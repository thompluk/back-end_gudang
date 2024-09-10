<?php

namespace App\Http\Controllers;

use App\Models\BuktiPengeluaranBarangDetail;
use App\Models\Item;
use App\Models\StockItem;
use Illuminate\Http\Request;

class BuktiPengeluaranBarangDetailController extends Controller
{
    public function index()
    {
        $bpb_detail = BuktiPengeluaranBarangDetail::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb_detail
        ], 200);
    }

    public function showbybpbid($bpb_id)
    {
        $bpb_detail = BuktiPengeluaranBarangDetail::where('bpb_id', $bpb_id)->get();

        if(count($bpb_detail) == 0){
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang Detail not found!',
                'data' => $bpb_detail
            ], 404);
        }

        foreach ($bpb_detail as $item) {
            if ($item->is_partial_delivery == 1) {
                $item->is_partial_delivery = '1';
            }else if ($item->is_partial_delivery == 0) {
                $item->is_partial_delivery = '0';
            }

            if ($item->is_delivered == 1) {
                $item->is_delivered = '1';
            }else if ($item->is_delivered == 0) {
                $item->is_delivered = '0';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb_detail
        ], 200);
    }

    public function destroy($id)
    {
        $bpb_detail = BuktiPengeluaranBarangDetail::find($id);

        if ($bpb_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang Detail not found!',
                'data' => $bpb_detail
            ], 404);
        } else {
            $bpb_detail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang Detail deleted successfully!',
            ], 200);
        }
    }

    public function saveAll(Request $request, $bpb_id)
    {
        // Validasi data yang diterima
        $request->validate([
            // '*.id' => 'required|integer',
            '*.item_id' => 'nullable|integer',
            '*.item_name' => 'nullable|string',
            '*.no_edp' => 'nullable|string',
            '*.no_sn' => 'nullable|string',
            '*.quantity' => 'nullable|integer',
            '*.delivery_date' => 'nullable|date',
            '*.notes' => 'nullable|string',
            '*.is_partial_delivery' => 'nullable|boolean',
        ]);

        // Loop melalui setiap item dan simpan atau perbarui
        foreach ($request->all() as $data) {
    
            BuktiPengeluaranBarangDetail::updateOrCreate(
                ['id' => $data['id']], // kondisi untuk menemukan record
                [
                    'bpb_id' => $bpb_id,
                    'item_id' => $data['item_id'],
                    'item_name' => $data['item_name'],
                    'no_edp' => $data['no_edp'],
                    'no_sn' => $data['no_sn'],
                    'quantity' => $data['quantity'],
                    'delivery_date' => $data['delivery_date'],
                    'notes' => $data['notes'],
                    'is_partial_delivery' => $data['is_partial_delivery'],
                    'is_delivered' => 0,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data successfully saved or updated!',
        ], 200);
    }

    public function deliver($id)
    {
        $bpb_detail = BuktiPengeluaranBarangDetail::find($id);

        if ($bpb_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang Detail not found!',
                'data' => $bpb_detail
            ], 404);
        }

        if($bpb_detail->is_partial_delivery == 1){
            $item = Item::where('no_edp', $bpb_detail->no_edp)->where('no_sn', $bpb_detail->no_sn)->first();
            
            if($item == null){
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak terdapat Item yang memiliki nomor EDP dan S/N yang sama pada gudang!',
                    'data' => $item
                ], 400);
            }

            $stockitem = StockItem::where('id', $item->stock_id)->first();

            $bpb_detail
                ->update([
                    'item_id' => $item->id,
                    'is_delivered' => 1
                ]);
            
            $item
                ->update([
                    'is_in_stock' => 0,
                    'leaving_date' => $bpb_detail->delivery_date
                ]);

            $stockitem
                ->update([
                    'quantity' => $stockitem->quantity - 1
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang Detail successfully delivered!',
                'data' => $bpb_detail
            ]);
        }else{
            $item = Item::find($bpb_detail->item_id);

            if($item == null){
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak terdapat Item pada gudang!',
                    'data' => $item
                ], 400);
            }

            $stockitem = StockItem::where('id', $item->stock_id)->first();

            $bpb_detail->update([
                'is_delivered' => 1
            ]);

            $item
                ->update([
                    'is_in_stock' => 0,
                    'leaving_date' => $bpb_detail->delivery_date
                ]);

            $stockitem
                ->update([
                    'quantity' => $stockitem->quantity - 1
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang Detail successfully delivered!',
                'data' => $bpb_detail
            ]);
        }
    }
}
