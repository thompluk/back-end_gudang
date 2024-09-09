<?php

namespace App\Http\Controllers;

use App\Models\BuktiPengeluaranBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuktiPengeluaranBarangController extends Controller
{
    public function index()
    {
        $bpb = BuktiPengeluaranBarang::orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function indexpoumum()
    {
        $bpb = BuktiPengeluaranBarang::orderBy('date')->where('status', 'Done')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function indexDraft()
    {
        $bpb = BuktiPengeluaranBarang::where('status', 'Draft')
                                        ->orWhere('status', 'Returned')
                                        ->orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }
    public function indexOnApproval()
    {
        $bpb = BuktiPengeluaranBarang::where('status', 'On Approval')->orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }
    public function indexDone()
    {
        $bpb = BuktiPengeluaranBarang::where('status', 'Done')->orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function indexRejected()
    {
        $bpb = BuktiPengeluaranBarang::where('status', 'Rejected')->orderBy('date')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Bukti Pengeluaran Barang successfully retrieved!',
            'data' => $bpb
        ], 200);
    }

    public function show($id)
    {
        $bpb = BuktiPengeluaranBarang::find($id);

        if ($bpb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang not found!',
                'data' => $bpb
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang retrieved successfully!',
                'data' => $bpb
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {

        $bpb = BuktiPengeluaranBarang::find($id);

        if ($bpb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang not found!',
                'data' => $bpb
            ], 404);
        } else {
            $bpb->update([
                'no_po'=>$request->no_po,
                'delivery_by'=>$request->delivery_by,
                'delivery_date'=>$request->delivery_date,
                'customer'=>$request->customer,
                'customer_address'=>$request->customer_address,
                'customer_pic_name'=>$request->customer_pic_name,
                'customer_pic_phone'=>$request->customer_pic_phone,
                'approved_by'=>$request->approved_by,
                'approved_by_id'=>$request->approved_by_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang updated successfully!',
                'data' => $bpb
            ], 200);
        }
    }

    public function destroy($id)
    {
        $bpb = BuktiPengeluaranBarang::find($id);

        if ($bpb == null) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti Pengeluaran Barang not found!',
                'data' => $bpb
            ], 404);
        } else {
            $bpb->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bukti Pengeluaran Barang deleted successfully!',
            ], 200);
        }
    }

    public function create( Request $request)
    {
        $no_bpb = $this->generateNoBpb();

        $request_by = Auth::user()->name;
        $request_by_id = Auth::user()->id;

        $formattedDate = Carbon::now()->format('Y-m-d');

        $bpb = BuktiPengeluaranBarang::create([
            'no_bpb'=>$no_bpb,
            'status'=>'Draft',
            'salesman'=>$request->salesman,
            'date'=>$formattedDate,
            'no_po'=>$request->no_po,
            'delivery_by'=>$request->delivery_by,
            'delivery_date'=>$request->delivery_date,
            'customer'=>$request->customer,
            'customer_address'=>$request->customer_address,
            'customer_pic_name'=>$request->customer_pic_name,
            'customer_pic_phone'=>$request->customer_pic_phone,
            'request_by'=>$request_by,
            'request_by_id'=>$request_by_id,
            'request_by_date'=>$formattedDate,
            'approved_by' => null,
            'approved_by_id' => null,
            'approved_by_date' => null,
            'approved_by_status' => null,
            'remarks' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan Pembelian Barang successfully created!',
            'data' => $bpb,
        ], 200);
    }

    private function generateNoBpb()
    {
    return DB::transaction(function () {
        $currentDate = Carbon::now();
        $year = $currentDate->year;
        $month = str_pad($currentDate->month, 2, '0', STR_PAD_LEFT);

        // Cari nomor terbesar yang ada saat ini
        $lastNoBpb = BuktiPengeluaranBarang::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('no_bpb', 'desc')
            ->value('no_bpb');

        // Jika ada nomor sebelumnya, increment nomor tersebut, jika tidak, mulai dari 1
        if ($lastNoBpb) {
            $lastSequence = (int) substr($lastNoBpb, -3);
            $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '001';
        }

        $newNoBpb = 'bpb_'.$year . $month . $newSequence;

        return $newNoBpb;
    });
    }


}
