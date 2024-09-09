<?php

namespace App\Http\Controllers;

use App\Models\BuktiPengeluaranBarangDetail;
use Illuminate\Http\Request;

class BuktiPengeluaranBarangDetailController extends Controller
{
    public function index()
    {
        $bpb_detail = BuktiPengeluaranBarangDetail::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb_detail
        ], 200);
    }

    public function showbybpbid($bpb_id)
    {
        $bpb_detail = BuktiPengeluaranBarangDetail::where('bpb_id', $bpb_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb_detail
        ], 200);
    }

    public function destroy($id)
    {
        $bpb_detail = BuktiPengeluaranBarangDetail::find($id);

        if ($bpb_detail == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang Detail not found!',
                'data' => $bpb_detail
            ], 404);
        } else {
            $bpb_detail->delete();

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
            '*.quantity' => 'nullable|integer',
            '*.notes' => 'nullable|string',
        ]);

        // Loop melalui setiap item dan simpan atau perbarui
        foreach ($request->all() as $data) {
    
            BuktiPengeluaranBarangDetail::updateOrCreate(
                ['id' => $data['id']], // kondisi untuk menemukan record
                [
                    'bpb_id' => $bpb_id,
                    'item_id' => $data['item_id'],
                    'item_name' => $data['item_name'],
                    'no_edp' => $data['no_edp'],
                    'no_sn' => $data['no_sn'],
                    'quantity' => $data['quantity'],
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
