<?php

namespace App\Http\Controllers;

use App\Models\BuktiPengeluaranBarangDetailDetail;
use Illuminate\Http\Request;

class BuktiPengeluaranBarangDetailDetailController extends Controller
{
    public function index()
    {
        $bpb_detail_detail = BuktiPengeluaranBarangDetailDetail::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb_detail_detail
        ], 200);
    }

    public function showbybpbid($bpb_id)
    {
        $bpb_detail_detail = BuktiPengeluaranBarangDetailDetail::where('bpb_id', $bpb_id)->get();

        if(count($bpb_detail_detail) == 0){
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang Detail not found!',
                'data' => $bpb_detail_detail
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb_detail_detail
        ], 200);
    }

    public function destroy($id)
    {
        $bpb_detail_detail = BuktiPengeluaranBarangDetailDetail::find($id);

        if ($bpb_detail_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang Detail not found!',
                'data' => $bpb_detail_detail
            ], 404);
        } else {
            $bpb_detail_detail->delete();

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
            '*.notes' => 'nullable|string',
        ]);

        // Loop melalui setiap item dan simpan atau perbarui
        foreach ($request->all() as $data) {
    
            BuktiPengeluaranBarangDetailDetail::updateOrCreate(
                ['id' => $data['id']], // kondisi untuk menemukan record
                [
                    'item_id' => $data['item_id'],
                    'item_name' => $data['item_name'],
                    'no_edp' => $data['no_edp'],
                    'no_sn' => $data['no_sn'],
                    'notes' => $data['notes'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data successfully saved or updated!',
        ], 200);
    }
}
