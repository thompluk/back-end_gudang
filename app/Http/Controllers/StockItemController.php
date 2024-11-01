<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PurchaseOrderDetail;
use App\Models\StockItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                'detail.*.no_edp' => 'nullable|string|required_without:detail.*.no_sn',
                'detail.*.no_sn' => 'nullable|string|required_without:detail.*.no_edp',
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
                'detail.*.no_edp.required_without' => 'No EDP atau No SN wajib diisi minimal salah satu.',
                'detail.*.no_sn.required_without' => 'No SN atau No EDP wajib diisi minimal salah satu.',
                'detail.*.no_ppb.required' => 'No PBB wajib diisi',
                'detail.*.no_po.required' => 'No PO wajib diisi',
                'detail.*.unit_price.required' => 'Unit Price wajib diisi',
                'detail.*.item_unit.required' => 'Item Unit wajib diisi',
                'detail.*.arrival_date.required' => 'Arrival Date wajib diisi',
                'detail.*.receiver.required' => 'Receiver wajib diisi',
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
    
            Item::create(
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
        $ids = $request->input('stock_ids');

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

    public function upload(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv', // max file size 2MB
        ]);

        // Menggunakan PHPSpreadsheet untuk membaca file Excel
        $filePath = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($filePath);

        // Ambil data dari sheet pertama
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Ambil header dari baris pertama
        $headers = $sheetData[1]; // Mengambil header dari baris pertama

        if($headers['A'] !== 'Stock Name*' || $headers['B'] !== 'Tipe*' || $headers['C'] !== 'Prinsipal*' || $headers['D'] !== 'Item Name*' ||$headers['E'] !== 'No EDP*' 
            || $headers['F'] !== 'No. S/N*' || $headers['G'] !== 'Item Unit*' || $headers['H'] !== 'In Stock?*' || $headers['I'] !== 'No. PPB' ||$headers['J'] !== 'No. PO'
            || $headers['K'] !== 'Description' || $headers['L'] !== 'Unit Price' || $headers['M'] !== 'Remarks' || $headers['N'] !== 'Arrival Date' || $headers['O'] !== 'Leaving Date' 
            || $headers['P'] !== 'Receiver'){
            return response()->json([
                'success' => false,
                'message' => 'File format is not valid! Please use the template form.',
                'data'=> $headers,
            ], 404);
        }
        // Loop melalui data dan simpan ke database
        foreach ($sheetData as $rowIndex => $row) {
            if ($rowIndex == 1) continue; // Lewati header
        
            $stock_name = $row['A'] ?? null;
            $tipe = $row['B'] ?? null;
            $prinsipal = $row['C'] ?? null;
            $item_name = $row['D'] ?? null;
            $no_edp = $row['E'] ?? null;
            $no_sn = $row['F'] ?? null;
            $item_unit = $row['G'] ?? null;
            $in_stock = $row['H'] ?? null;
            $no_ppb = $row['I'] ?? null;
            $no_po = $row['J'] ?? null;
            $description = $row['K'] ?? null;
            $unit_price = $row['L'] ?? null;
            $remarks = $row['M'] ?? null;
            $arrival_date = $row['N'] ?? null;
            $leaving_date = $row['O'] ?? null;
            $receiver = $row['P'] ?? null;
        
            if (empty($stock_name) || empty($tipe) || empty($prinsipal || empty($item_name) || (empty($no_edp) && empty($no_sn)) || empty($item_unit) || empty($in_stock))) {
                // return response()->json(['message' => 'File uploaded successfully!','data' => $name]);
                Log::warning("Missing stock name or tipe or prinsipal or item name or no edp or no sn or item unit or in stock? at row $rowIndex");
                continue; // Lewati baris ini jika nama atau email kosong
            }
            $arr_date = null;
            $leav_date = null;

            if (!empty($arrival_date)) {
                try {
                    $arr_date = Carbon::createFromFormat('d/m/Y', $arrival_date);
                    // Format sesuai, lakukan sesuatu dengan $arrival_date_carbon
                } catch (\Exception $e) {
                    $arr_date = null;
                }
            }
            if (!empty($leaving_date)) {
                try {
                    $leav_date = Carbon::createFromFormat('d/m/Y', $leaving_date);
                    // Format sesuai, lakukan sesuatu dengan $leaving_date_carbon
                } catch (\Exception $e) {
                    $leav_date = null;
                }
            }

            $is_in_stock = $in_stock == 'Ya' ? true : false;

            $stockItem = StockItem::where('stock_name', $stock_name)->where('tipe', $tipe)->where('prinsipal', $prinsipal)->first();

            if ($stockItem == null) {
                $new_stockItem =StockItem::create([
                    'stock_name' => $stock_name,
                    'tipe' => $tipe,
                    'prinsipal' => $prinsipal,
                    'prinsipal_id' => null,
                    'quantity' => 1,
                ]);

                Item::create([
                    'stock_id' => $new_stockItem->id,
                    'item_name' => $item_name,
                    'no_edp' => $no_edp,
                    'no_sn' => $no_sn,
                    'no_ppb' => $no_ppb,
                    'no_po' => $no_po,
                    'description' => $description,
                    'unit_price' => $unit_price,
                    'remarks' => $remarks,
                    'item_unit' => $item_unit,
                    'is_in_stock' => $is_in_stock,
                    'arrival_date' => $arr_date,
                    'leaving_date' => $leav_date,
                    'receiver' => $receiver,
                    'receiver_id' => null,
                ]);
            }else{
                if($is_in_stock == true){
                    $stockItem->update([
                        'quantity' => ($stockItem->quantity + 1),
                    ]);
                }

                Item::create([
                    'stock_id' => $stockItem->id,
                    'item_name' => $item_name,
                    'no_edp' => $no_edp,
                    'no_sn' => $no_sn,
                    'no_ppb' => $no_ppb,
                    'no_po' => $no_po,
                    'description' => $description,
                    'unit_price' => $unit_price,
                    'remarks' => $remarks,
                    'item_unit' => $item_unit,
                    'is_in_stock' => $is_in_stock,
                    'arrival_date' => $arr_date,
                    'leaving_date' => $leav_date,
                    'receiver' => $receiver,
                    'receiver_id' => null,
                ]);
            }
        }

        return response()->json(['message' => 'File uploaded successfully!']);
    }
}
