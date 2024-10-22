<?php

namespace App\Http\Controllers;

use App\Models\PengembalianBarangDetail;
use Illuminate\Http\Request;

class PengembalianBarangDetailController extends Controller
{
    public function index()
    {
        $pengembalianBarangDetail = PengembalianBarangDetail::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Pengembalian Barang Detail successfully retrieved!',
            'data' => $pengembalianBarangDetail
        ], 200);
    }

    public function showbyid($pengembalian_barang_id)
    {
        $pengembalianBarangDetail = PengembalianBarangDetail::where('pengembalian_barang_id', $pengembalian_barang_id)->get();

        if(count($pengembalianBarangDetail) == 0){
            return response()->json([
                'success' => false,
                'message' => 'Pengembalian Barang Detail not found!',
                'data' => $pengembalianBarangDetail
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'All Pengembalian Barang Detail successfully retrieved!',
            'data' => $pengembalianBarangDetail
        ], 200);
    }

    public function destroy($id)
    {
        $pengembalianBarangDetail = PengembalianBarangDetail::find($id);

        if ($pengembalianBarangDetail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengembalian Barang Detail Detail not found!',
                'data' => $pengembalianBarangDetail
            ], 404);
        } else {
            $pengembalianBarangDetail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian Barang Detail Detail deleted successfully!',
            ], 200);
        }
    }

    public function saveAll(Request $request, $pengembalian_barang_id)
    {
        // Validasi data yang diterima
        $request->validate([
            '*.surat_jalan_detail_id' => 'nullable|integer',
            '*.stock_material_id' => 'nullable|integer',
            '*.nama_barang' => 'nullable|string',
            '*.quantity_dikirim' => 'nullable|integer',
            '*.quantity_dikembalikan' => 'nullable|integer',
            '*.keterangan' => 'nullable|string',
        ]);

        // Loop melalui setiap item dan simpan atau perbarui
        foreach ($request->all() as $data) {
    
            PengembalianBarangDetail::updateOrCreate(
                ['id' => $data['id']], // kondisi untuk menemukan record
                [
                    'pengembalian_barang_id' => $pengembalian_barang_id,
                    'stock_material_id' => $data['stock_material_id'],
                    'surat_jalan_detail_id' => $data['surat_jalan_detail_id'],
                    'nama_barang' => $data['nama_barang'],
                    'quantity_dikirim' => $data['quantity_dikirim'],
                    'quantity_dikembalikan' => $data['quantity_dikembalikan'],
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
