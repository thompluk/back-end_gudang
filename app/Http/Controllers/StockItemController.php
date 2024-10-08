<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PurchaseOrderDetail;
use App\Models\StockItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockItemController extends Controller
{
    public function index()
    {
        $stockItem = StockItem::orderBy('stock_name')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Stock Item successfully retrieved!',
            'data' => $stockItem
        ], 200);
    }

    public function show($id)
    {
        $stockItem = StockItem::find($id);

        if ($stockItem == null) {
            return response()->json([
                'success' => false,
                'message' => 'Stock Item not found!',
                'data' => $stockItem
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Stock Item retrieved successfully!',
                'data' => $stockItem
            ], 200);
        }
    } 

    public function stockiteminit(Request $request, $po_detail_id)
    {
        DB::beginTransaction();
        try {
        $validation = Validator::make(
            $request->all(),
            [
                'stock_name' => 'required',
                'tipe' => 'required',
            ],
            [
                'stock_name.required' => 'Stock Name wajib diisi!',
                'tipe.required' => 'Tipe wajib diisi!',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        if($request->tipe == 'LOKAL'){
            $prinsipal = 'LOKAL';
            $prinsipal_id = null;
        }else{
            $prinsipal = $request->prinsipal;
            $prinsipal_id = $request->prinsipal_id;
        }

        $stock = StockItem::create([
            'stock_name' => $request->stock_name,
            'tipe' => $request->tipe,
            'prinsipal' => $prinsipal,
            'prinsipal_id' => $prinsipal_id,
            'quantity' => $request->quantity,
        ]);

        $validation = Validator::make(
            $request->all(),
            [
                'detail.*.item_name' => 'required|string',
                'detail.*.no_edp' => 'required|string',
                'detail.*.no_sn' => 'required|string', 
                'detail.*.no_ppb' => 'required|string',
                'detail.*.no_po' => 'required|string',
                'detail.*.description' => 'nullable|string', 
                'detail.*.unit_price' => 'required|numeric',
                'detail.*.remarks' => 'nullable|string', 
                'detail.*.item_unit' => 'required|string',
                'detail.*.arrival_date' => 'required|date',
                'detail.*.receiver' => 'required|string',
                'detail.*.receiver_id' => 'required|integer',
            ],
            [
                'detail.*.item_name.required' => 'Item Name wajib diisi',
                'detail.*.no_edp.required' => 'No EDP wajib diisi',
                'detail.*.no_sn.required' => 'No SN wajib diisi', 
                'detail.*.no_ppb.required' => 'No PBB wajib diisi',
                'detail.*.no_po.required' => 'No PO wajib diisi',
                'detail.*.unit_price.required' => 'Unit Price wajib diisi',
                'detail.*.item_unit.required' => 'Item Unit wajib diisi',
                'detail.*.arrival_date.required' => 'Arrival Date wajib diisi',
                'detail.*.receiver.required' => 'receiver wajib diisi',
                'detail.*.receiver_id.required' => 'Receiver ID wajib diisi',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        // Loop melalui setiap item dan simpan atau perbarui

        foreach ($request->input('detail') as $data) {
    
            Item::updateOrCreate(
                ['id' => $data['id']], // kondisi untuk menemukan record
                [
                    'stock_id' => $stock->id,
                    'item_name' => $data['item_name'],
                    'no_edp' => $data['no_edp'],
                    'no_sn' => $data['no_sn'],
                    'no_ppb' => $data['no_ppb'],
                    'no_po' => $data['no_po'],
                    'description' => $data['description'],
                    'unit_price' => $data['unit_price'],
                    'remarks' => $data['remarks'],
                    'item_unit' => $data['item_unit'],
                    'arrival_date' => $data['arrival_date'],
                    'receiver' => $data['receiver'],
                    'receiver_id' => $data['receiver_id'],
                    'is_in_stock' => true,
                    'leaving_date' => null,
                ]
            );
        }

        $po_detail = PurchaseOrderDetail::find($po_detail_id);

        if ($po_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order Detail not found!',
                'data' => $po_detail
            ], 404);
        } else {
            $po_detail->update([
                'is_items_created' => 1,
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Prinsipal successfully created!',
            'data' => $stock,
        ], 200);

        } catch (\Exception $e) {
            // Jika terjadi kesalahan, rollback semua perubahan
            DB::rollBack();
    
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        $stock = StockItem::find($id);

        if ($stock == null) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found!',
                'data' => $stock
            ], 404);
        } else {
            $stock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock deleted successfully!',
            ], 200);
        }
    }

    public function stockItemSelect(Request $request)
    {
        $ids = $request->input('stock_detail_ids');

        // $ppb_detail_id = PurchaseOrderDetail::select('ppb_detail_id')->where('ppb_detail_id', '!=', null)->get();

        if (!empty($ids)) {
            
            $stockItem = StockItem::whereNotIn('id', $ids)->get();
        } else {
            $stockItem = StockItem::all();
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock Item successfully retrieved!',
            'data' => $stockItem,
        ], 200);
    }

    public function indexDashboard()
    {
        // Mengambil data stock_name dan quantity saja, disortir berdasarkan updated_at paling baru
        $stockItem = StockItem::orderBy('updated_at', 'desc')
            ->take(10)
            ->get(['stock_name', 'quantity']); // Pilih kolom yang ingin diambil

        return response()->json([
            'success' => true,
            'message' => 'All Stock Item successfully retrieved!',
            'data' => $stockItem
        ], 200);
    }
}
