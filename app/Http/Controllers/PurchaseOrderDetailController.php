<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderDetailController extends Controller
{
    public function index()
    {
        $po_detail = PurchaseOrderDetail::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po_detail
        ], 200);
    }

    public function showbypoid($po_id)
    {
        $po_detail = PurchaseOrderDetail::where('po_id', $po_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po_detail
        ], 200);
    }

    public function create(Request $request)
    {

        $validation = Validator::make(
            $request->all(),
            [
                'po_id' => 'required',
            ],
            [
                'po_id.required' => 'PO id wajib diisi!',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        $po_detail = PurchaseOrderDetail::create([
            'po_id' => $request->po_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perimaan Pembelian Barang Detail successfully created!',
            'data' => $po_detail
        ], 200);
    }

    public function destroy($id)
    {
        $po_detail = PurchaseOrderDetail::find($id);

        if ($po_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order Detail not found!',
                'data' => $po_detail
            ], 404);
        } else {
            $po_detail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order Detail deleted successfully!',
            ], 200);
        }
    }

    public function saveAll(Request $request, $po_id)
    {

        // Validasi data yang diterima
        $request->validate([
            // '*.id' => 'required|integer',
            '*.item' => 'nullable|string',
            '*.no_ppb' => 'nullable|string',
            '*.ppb_detail_id' => 'nullable|integer', 
            '*.description' => 'nullable|string',
            '*.quantity' => 'nullable|integer',
            '*.unit_price' => 'nullable|numeric', 
            '*.discount' => 'nullable|numeric',
            '*.amount' => 'nullable|numeric', 
            '*.remarks' => 'nullable|string',
            '*.item_unit' => 'nullable|string',
        ]);

        // Loop melalui setiap item dan simpan atau perbarui
        foreach ($request->all() as $data) {
    
            PurchaseOrderDetail::updateOrCreate(
                ['id' => $data['id']], // kondisi untuk menemukan record
                [
                    'po_id' => $po_id,
                    'item' => $data['item'],
                    'no_ppb' => $data['no_ppb'],
                    'ppb_detail_id' => $data['ppb_detail_id'],
                    'description' => $data['description'],
                    'quantity' => $data['quantity'],
                    'unit_price' => $data['unit_price'],
                    'discount' => $data['discount'],
                    'amount' => $data['amount'],
                    'remarks' => $data['remarks'],
                    'item_unit' => $data['item_unit'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data successfully saved or updated!',
        ], 200);
    }
}
