<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PurchaseOrderDetail;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\Count;

class ItemController extends Controller
{

    public function index()
    {
        $item = Item::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Item successfully retrieved!',
            'data' => $item
        ], 200);
    }

    public function itemSelect(Request $request)
    {
        $ids = $request->input('item_ids');

        if (!empty($ids)) {
            
            $item = Item::
            // whereNotIn('id', function($query) {
            //     $query->select('item_id')->where('item_id', '!=', null)
            //         ->from('bukti_pengeluaran_barang');
            // })->
            where('is_in_stock', true)
            ->whereNotIn('id', $ids)
            ->get();
        } else {
            $item = Item::
            // whereNotIn('id', function($query) {
            //     $query->select('item_id')->where('item_id', '!=', null)
            //         ->from('bukti_pengeluaran_barang');
            // })
            where('is_in_stock', true)
            ->get();
        }


        return response()->json([
            'success' => true,
            'message' => 'Stock Item successfully retrieved!',
            'data' => $item,
        ], 200);
    }

    public function showbystockid($stock_id)
    {
        $item = Item::where('stock_id', $stock_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'All Item successfully retrieved!',
            'data' => $item
        ], 200);
    }

    public function saveAll(Request $request, $stock_id, $po_detail_id)
    {

        DB::beginTransaction();
        try {
            $stock = StockItem::find($stock_id);

            $req = $request->all();

            if ($stock == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock Item not found!',
                    'data' => $stock
                ], 404);
            }
                
            // Validasi data yang diterima
            $request->validate([
                // '*.id' => 'required|integer',
                '*.item_name' => 'required|string',
                '*.no_edp' => 'required|string',
                '*.no_sn' => 'required|string', 
                '*.no_ppb' => 'required|string',
                '*.no_po' => 'required|string',
                '*.description' => 'nullable|string', 
                '*.unit_price' => 'required|numeric',
                '*.remarks' => 'nullable|string', 
                '*.item_unit' => 'required|string',
                '*.arrival_date' => 'required|date',
                '*.receiver' => 'required|string',
                '*.receiver_id' => 'required|integer',
            ],
            [
                // '*.item_name.required' => 'Item Name wajib diisi',
                '*.no_edp.required' => 'No EDP wajib diisi',
                '*.no_sn.required' => 'No SN wajib diisi', 
                // '*.no_ppb.required' => 'No PBB wajib diisi',
                // '*.no_po.required' => 'No PO wajib diisi',
                // '*.unit_price.required' => 'Unit Price wajib diisi',
                // '*.item_unit.required' => 'Item Unit wajib diisi',
                // '*.arrival_date.required' => 'Arrival Date wajib diisi',
                // '*.receiver.required' => 'receiver wajib diisi',
                // '*.receiver_id.required' => 'Receiver ID wajib diisi',
            ]);

            // Loop melalui setiap item dan simpan atau perbarui
            foreach ($request->all() as $data) {
        
                Item::updateOrCreate(
                    ['id' => $data['id']], // kondisi untuk menemukan record
                    [
                        'stock_id' => $stock_id,
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

            $stock->update([
                'quantity' => $stock->quantity + Count($req),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data successfully saved or updated!',
            ], 200);

        }catch (\Exception $e) {
            // Jika terjadi kesalahan, rollback semua perubahan
            DB::rollBack();
    
            return response()->json(['error' => $e->getMessage()], 400);
        }
        
    }
}
