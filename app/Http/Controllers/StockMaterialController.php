<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\StockMaterial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMaterialController extends Controller
{
    public function index()
    {
        $stockMaterial = StockMaterial::orderBy('stock_name')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Stock Material successfully retrieved!',
            'data' => $stockMaterial
        ], 200);
    }

    public function show($id)
    {
        $stockMaterial = StockMaterial::find($id);

        if ($stockMaterial == null) {
            return response()->json([
                'success' => false,
                'message' => 'Stock Material not found!',
                'data' => $stockMaterial
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Stock Material retrieved successfully!',
                'data' => $stockMaterial
            ], 200);
        }
    } 

    public function stockmaterialinit(Request $request, $po_detail_id)
    {
        
        $po_detail = PurchaseOrderDetail::find($po_detail_id);
        $po = PurchaseOrder::find($po_detail->po_id);

        $receiver = Auth::user()->name;
        $receiver_id = Auth::user()->id;

        $formattedDate = Carbon::now()->format('Y-m-d');

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

        if ($request->is_stock_exist == 'ya') {
            $stock = StockMaterial::find($request->stock_id);
            $stock->update([
                'quantity' => $stock->quantity + $po_detail->quantity,
            ]);
        }else{
            $stock = StockMaterial::create([
                'stock_name' => $po_detail->item,
                'quantity' => $po_detail->quantity,
                'no_ppb' => $po_detail->no_ppb,
                'no_po' => $po->no_po,
                'description' => $po_detail->description,
                'unit_price' => $po_detail->unit_price,
                'remarks' => $po_detail->remarks,
                'item_unit' => $po_detail->item_unit,
                'arrival_date' => $formattedDate,
                'receiver' => $receiver,
                'receiver_id' => $receiver_id,
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Stock Material successfully created!',
            'data' => $stock,
        ], 200);
    }

    public function destroy($id)
    {
        $stock = StockMaterial::find($id);

        if ($stock == null) {
            return response()->json([
                'success' => false,
                'message' => 'Stock Material not found!',
                'data' => $stock
            ], 404);
        } else {
            $stock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock Material deleted successfully!',
            ], 200);
        }
    }

    public function stockMaterialSelect(Request $request)
    {
        $ids = $request->input('stock_material_ids');

        // $ppb_detail_id = PurchaseOrderDetail::select('ppb_detail_id')->where('ppb_detail_id', '!=', null)->get();

        if (!empty($ids)) {
            
            $stockMaterial = StockMaterial::whereNotIn('id', $ids)->get();
        } else {
            $stockMaterial = StockMaterial::all();
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock Material successfully retrieved!',
            'data' => $stockMaterial,
        ], 200);
    }

    public function indexDashboard()
    {
        // Mengambil data stock_name dan quantity saja, disortir berdasarkan updated_at paling baru
        $stockMaterial = StockMaterial::orderBy('updated_at', 'desc')
            ->take(10)
            ->get(['stock_name', 'quantity']); // Pilih kolom yang ingin diambil

        return response()->json([
            'success' => true,
            'message' => 'All Stock Item successfully retrieved!',
            'data' => $stockMaterial
        ], 200);
    }
}
