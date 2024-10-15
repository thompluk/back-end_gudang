<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PermintaanPembelianBarang;
use App\Models\PermintaanPembelianBarangDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermintaanPembelianBarangController extends Controller
{
    public function index()
    {
        $ppb = PermintaanPembelianBarang::orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Permintaan Pembelian Barang successfully retrieved!',
            'data' => $ppb
        ], 200);
    }

    public function indexppbumum()
    {
        $ppb = PermintaanPembelianBarang::orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Permintaan Pembelian Barang successfully retrieved!',
            'data' => $ppb
        ], 200);
    }

    public function indexDraft()
    {
        $user_id = Auth::user()->id;
        $ppb = PermintaanPembelianBarang::where('pemohon_id', $user_id)
                                        ->where('status', 'Draft')
                                        ->orWhere('status', 'Returned')
                                        ->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Permintaan Pembelian Barang successfully retrieved!',
            'data' => $ppb
        ], 200);
    }
    public function indexOnApproval()
    {
        $user_id = Auth::user()->id;
        $ppb = PermintaanPembelianBarang::where('pemohon_id', $user_id)->where('status', 'On Approval')->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Permintaan Pembelian Barang successfully retrieved!',
            'data' => $ppb
        ], 200);
    }
    public function indexDone()
    {
        $user_id = Auth::user()->id;
        $ppb = PermintaanPembelianBarang::where('pemohon_id', $user_id)->where('status', 'Done')->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Permintaan Pembelian Barang successfully retrieved!',
            'data' => $ppb
        ], 200);
    }

    public function indexRejected()
    {
        $user_id = Auth::user()->id;
        $ppb = PermintaanPembelianBarang::where('pemohon_id', $user_id)->where('status', 'Rejected')->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Permintaan Pembelian Barang successfully retrieved!',
            'data' => $ppb
        ], 200);
    }

    public function show($id)
    {
        $ppb = PermintaanPembelianBarang::find($id);

        if ($ppb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan Pembelian Barang not found!',
                'data' => $ppb
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan Pembelian Barang retrieved successfully!',
                'data' => $ppb
            ], 200);
        }
    } 

    public function create( Request $request)
    {
        $no_ppb = $this->generateNoPpb();

        $pemohon = Auth::user()->name;
        $pemohon_id = Auth::user()->id;

        $formattedDate = Carbon::now()->format('Y-m-d');

        $ppb = PermintaanPembelianBarang::create([
            'no_ppb'=>$no_ppb,
            'tanggal'=>$formattedDate,
            'status' => 'Draft',
            'pemohon'=>$pemohon,
            'pemohon_id'=>$pemohon_id,
            'mengetahui'=>$request->mengetahui,
            'mengetahui_id'=>$request->mengetahui_id,
            'menyetujui'=>$request->menyetujui,
            'menyetujui_id'=>$request->menyetujui_id,
            'remarks'=>null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan Pembelian Barang successfully created!',
            'data' => $ppb,
            // 'data_detail' => $ppb_detail
        ], 200);
    }

    private function generateNoPpb()
    {
    return DB::transaction(function () {
        $currentDate = Carbon::now();
        $year = $currentDate->year;
        $month = str_pad($currentDate->month, 2, '0', STR_PAD_LEFT);

        // Cari nomor terbesar yang ada saat ini
        $lastNoPpb = PermintaanPembelianBarang::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('no_ppb', 'desc')
            ->value('no_ppb');

        // Jika ada nomor sebelumnya, increment nomor tersebut, jika tidak, mulai dari 1
        if ($lastNoPpb) {
            $lastSequence = (int) substr($lastNoPpb, -3);
            $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '001';
        }

        $newNoPpb = 'ppb_'.$year . $month . $newSequence;

        return $newNoPpb;
    });
    }

    public function update(Request $request, $id)
    {

        $ppb = PermintaanPembelianBarang::find($id);

        if ($ppb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan Pembelian Barang not found!',
                'data' => $ppb
            ], 404);
        } else {
            $ppb->update([
                'mengetahui'=>$request->mengetahui,
                'mengetahui_id'=>$request->mengetahui_id,
                'menyetujui'=>$request->menyetujui,
                'menyetujui_id'=>$request->menyetujui_id,
                // 'pruchasing'=>$request->pruchasing,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan Pembelian Barang updated successfully!',
                'data' => $ppb
            ], 200);
        }
    }

    public function destroy($id)
    {
        $ppb = PermintaanPembelianBarang::find($id);
        $ppb_detail = PermintaanPembelianBarangDetail::where('ppb_id', $id)->get();

        if ($ppb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan Pembelian Barang not found!',
                'data' => $ppb
            ], 404);
        } else {
            $ppb->delete();

            // if($ppb_detail != null){
            //     $ppb_detail->delete();
            // }

            return response()->json([
                'success' => true,
                'message' => 'Permintaan Pembelian Barang deleted successfully!',
            ], 200);
        }
    }

    public function post( $id)
    {

        $ppb = PermintaanPembelianBarang::find($id);
        $ppb_detail = PermintaanPembelianBarangDetail::where('ppb_id', $id)->get();

        if ($ppb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan Pembelian Barang not found!',
                'data' => $ppb
            ], 404);
        }

        if (count($ppb_detail) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Item Permintaan Pembelian Barang minimal 1 item!',
                'data' => $ppb
            ], 404);
        }

        if( $ppb->mengetahui == null || $ppb->menyetujui_id == null || $ppb->menyetujui == null || $ppb->menyetujui_id == null){
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh ada data PPB kosong'
            ], 400);
        }

        foreach($ppb_detail as $item){
            if($item->nama_barang == null || $item->kode == null || $item->spesifikasi == null || $item->quantity == null || $item->expected_eta == null || $item->project_and_customer == null){
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak boleh ada data Item yang kosong'
                ], 400);
            }
        }
        
        $ppb->update([
            'status'=>'On Approval',
            'mengetahui_status'=>'Waiting for Confirmation',
            'menyetujui_status'=>'Waiting for Confirmation',
            'purchasing_status'=>'Waiting for Confirmation',
            'remarks'=>null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan Pembelian Barang updated successfully!',
            'data' => $ppb
        ], 200);
        
    }

    public function indexDashboard2()
    {
        // Mengambil status 'on approval' dan 'done', mengelompokkan berdasarkan bulan dari updated_at
        $ppb = PermintaanPembelianBarang::selectRaw('
                COUNT(*) as total,
                status,
                DATE_FORMAT(tanggal, "%m") as month
            ')
            ->whereIn('status', ['On Approval', 'Done']) // Memfilter status
            ->groupBy('month', 'status') // Mengelompokkan berdasarkan bulan numerik dan status
            ->orderBy('month', 'asc') // Mengurutkan berdasarkan bulan terbaru
            ->get();

        $datas = collect([]);

        foreach($ppb->groupBy('month') as $item){
                $storeData['Month'] = $item->first()->month; // Nama bulan
                $storeData['On Approval'] = $item->where('status', 'On Approval')->sum('total') ?? 0;
                $storeData['Done'] = $item->where('status', 'Done')->sum('total') ?? 0;

                $datas->add($storeData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order status per month retrieved successfully!',
            'data' => $datas 
        ], 200);
    }

    public function indexDashboard()
    {
        // Mengambil status 'on approval' dan 'done', mengelompokkan berdasarkan bulan dari updated_at
        $ppb = PermintaanPembelianBarang::selectRaw('
                status,
                DATE_FORMAT(tanggal, "%m") as month
            ')
            ->whereIn('status', ['On Approval', 'Done']) // Memfilter status
            ->get();

        $datas = collect([]);

        for($i = 1;$i<13;$i++){
                $bulan = Carbon::create(null, $i, 1)->format('F');
                $storeData['Month'] = $bulan; // Nama bulan
                $storeData['On Approval'] = $ppb->where('status', 'On Approval')->where('month', '=', $i)->count() ?? 0;
                $storeData['Done'] = $ppb->where('status', 'Done')->where('month', '=', $i)->count() ?? 0;
                $datas->add($storeData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order status per month retrieved successfully!',
            'data' => $datas 
        ], 200);
    }
}
