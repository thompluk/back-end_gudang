<?php

namespace App\Http\Controllers;

use App\Models\PermintaanPembelianBarang;
use App\Models\PermintaanPembelianBarangDetail;
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
            'ppb_id' => $request->ppb_id
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
