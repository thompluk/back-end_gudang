<?php

namespace App\Http\Controllers;

use App\Models\SuratJalanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuratJalanDetailController extends Controller
{
    public function index()
    {
        $surat_jalan_detail = SuratJalanDetail::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan Detail successfully retrieved!',
            'data' => $surat_jalan_detail
        ], 200);
    }

    public function showbysuratjalanid($surat_jalan_id)
    {
        $surat_jalan_detail = SuratJalanDetail::where('surat_jalan_id', $surat_jalan_id)->get();

        if(count($surat_jalan_detail) == 0){
            return response()->json([
                'success' => false,
                'message' => 'Surat Jalan Detail not found!',
                'data' => $surat_jalan_detail
            ], 404);
        }

        foreach ($surat_jalan_detail as $item) {
            if ($item->is_dikembalikan == 1) {
                $item->is_dikembalikan = '1';
            }else if ($item->is_dikembalikan == 0) {
                $item->is_dikembalikan = '0';
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan Detail successfully retrieved!',
            'data' => $surat_jalan_detail
        ], 200);
    }

    public function create(Request $request)
    {

        $validation = Validator::make(
            $request->all(),
            [
                'stock_item_id' => 'required',
            ],
            [
                'stock_item_id.required' => 'Item wajib diisi!',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        $surat_jalan_detail = SuratJalanDetail::create([
            'ppb_id' => $request->stock_item_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat Jalan Detail successfully created!',
            'data' => $surat_jalan_detail
        ], 200);
    }

    public function destroy($id)
    {
        $surat_jalan_detail = SuratJalanDetail::find($id);

        if ($surat_jalan_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Surat Jalan Detail Detail not found!',
                'data' => $surat_jalan_detail
            ], 404);
        } else {
            $surat_jalan_detail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan Detail Detail deleted successfully!',
            ], 200);
        }
    }

    public function saveAll(Request $request, $surat_jalan_id)
    {
        // Validasi data yang diterima
        $request->validate([
            // '*.no_edp' => 'nullable|string',
            // '*.no_sn' => 'nullable|string',
            '*.stock_item_id' => 'nullable|integer',
            '*.nama_barang' => 'nullable|string',
            '*.quantity' => 'nullable|integer',
            '*.is_dikembalikan' => 'nullable|boolean',
            '*.keterangan' => 'nullable|string',
        ]);

        // Loop melalui setiap item dan simpan atau perbarui
        foreach ($request->all() as $data) {
    
            SuratJalanDetail::updateOrCreate(
                ['id' => $data['id']], // kondisi untuk menemukan record
                [
                    'surat_jalan_id' => $surat_jalan_id,
                    'stock_item_id' => $data['stock_item_id'],
                    // 'no_edp' => $data['no_edp'],
                    // 'no_sn' => $data['no_sn'],
                    'nama_barang' => $data['nama_barang'],
                    'quantity' => $data['quantity'],
                    'is_dikembalikan' => $data['is_dikembalikan'] ,
                    'keterangan' => $data['keterangan'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data successfully saved or updated!',
        ], 200);
    }
}
