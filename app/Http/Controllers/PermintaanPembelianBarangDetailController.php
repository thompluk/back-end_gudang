<?php

namespace App\Http\Controllers;

use App\Models\PermintaanPembelianBarang;
use App\Models\PermintaanPembelianBarangDetail;
use App\Models\PurchaseOrderDetail;
use Brick\Math\BigInteger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermintaanPembelianBarangDetailController extends Controller
{
    public function index()
    {
        $ppb_detail = PermintaanPembelianBarangDetail::orderBy('id')->get();

        // $ppb_detail_with_keys = $ppb_detail->map(function ($item, $index) {
        //     $item['key'] = $index + 1;
        //     return $item;
        // });

        return response()->json([
            'success' => true,
            'message' => 'All Permintaan Pembelian Barang successfully retrieved!',
            'data' => $ppb_detail
        ], 200);
    }

    public function ppbDetailSelect(Request $request)
    {
        $ids = $request->input('ppb_detail_ids');

        // $ppb_detail_id = PurchaseOrderDetail::select('ppb_detail_id')->where('ppb_detail_id', '!=', null)->get();

        if (!empty($ids)) {
            
            $ppb_detail = PermintaanPembelianBarangDetail::with('permintaanPembelianBarang')
            ->whereHas('permintaanPembelianBarang', function ($query) {
                $query->where('status', 'Done');
            })
            ->whereNotIn('id', function($query) {
                $query->select('ppb_detail_id')->where('ppb_detail_id', '!=', null)
                    ->from('purchase_order_detail');
            })
            ->whereNotIn('id', $ids)
            ->get();
        } else {
            $ppb_detail = PermintaanPembelianBarangDetail::with('permintaanPembelianBarang')
            ->whereHas('permintaanPembelianBarang', function ($query) {
                $query->where('status', 'Done');
            })
            ->whereNotIn('id', function($query) {
                $query->select('ppb_detail_id')->where('ppb_detail_id', '!=', null)
                    ->from('purchase_order_detail');
            })
            ->get();
        }

        // Mengambil semua pengguna kecuali pengguna yang sedang login
        // $ppb_detail = PermintaanPembelianBarangDetail::with('permintaanPembelianBarang')
        //     ->whereHas('permintaanPembelianBarang', function ($query) {
        //         $query->where('status', 'Done');
        //     })
        //     ->whereNotIn('id', function($query) {
        //         $query->select('ppb_detail_id')
        //             ->from('purchase_order_detail');
        //     })
        //     ->get();

        foreach ($ppb_detail as $detail) {
            $detail->setAttribute('no_ppb', $detail->permintaanPembelianBarang->no_ppb);
        }

        return response()->json([
            'success' => true,
            'message' => 'PPB Detail successfully retrieved!',
            'data' => $ppb_detail,
        ], 200);
    }

    public function showbyppbid($ppb_id)
    {
        $ppb_detail = PermintaanPembelianBarangDetail::where('ppb_id', $ppb_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'All Permintaan Pembelian Barang successfully retrieved!',
            'data' => $ppb_detail
        ], 200);
    }

    public function create(Request $request)
    {

        $validation = Validator::make(
            $request->all(),
            [
                'ppb_id' => 'required',
            ],
            [
                'ppb_id.required' => 'PPB id wajib diisi!',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        $ppb_detail = PermintaanPembelianBarangDetail::create([
            'ppb_id' => $request->ppb_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perimaan Pembelian Barang Detail successfully created!',
            'data' => $ppb_detail
        ], 200);
    }

    public function destroy($id)
    {
        $ppb_detail = PermintaanPembelianBarangDetail::find($id);

        if ($ppb_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan Pembelian Barang Detail not found!',
                'data' => $ppb_detail
            ], 404);
        } else {
            $ppb_detail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan Pembelian Barang Detail deleted successfully!',
            ], 200);
        }
    }

    public function saveAll(Request $request, $ppb_id)
    {
        // Validasi data yang diterima
        $request->validate([
            // '*.id' => 'required|integer',
            '*.nama_barang' => 'nullable|string',
            '*.kode' => 'nullable|string',
            '*.spesifikasi' => 'nullable|string',
            '*.quantity' => 'nullable|integer',
            '*.expected_eta' => 'nullable|date',
            '*.project_and_customer' => 'nullable|string',
        ]);

        // Loop melalui setiap item dan simpan atau perbarui
        foreach ($request->all() as $data) {
    
            PermintaanPembelianBarangDetail::updateOrCreate(
                ['id' => $data['id']], // kondisi untuk menemukan record
                [
                    'nama_barang' => $data['nama_barang'],
                    'ppb_id' => $ppb_id,
                    'kode' => $data['kode'],
                    'spesifikasi' => $data['spesifikasi'],
                    'quantity' => $data['quantity'],
                    'expected_eta' => $data['expected_eta'],
                    'project_and_customer' => $data['project_and_customer'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data successfully saved or updated!',
        ], 200);
    }
}
