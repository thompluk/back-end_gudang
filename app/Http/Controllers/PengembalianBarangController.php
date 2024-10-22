<?php

namespace App\Http\Controllers;

use App\Models\PengembalianBarang;
use App\Models\PengembalianBarangDetail;
use App\Models\StockMaterial;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengembalianBarangController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $pengembalianBarang = PengembalianBarang::where('penerima_id', $user_id)->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Pengembalian Barang successfully retrieved!',
            'data' => $pengembalianBarang
        ], 200);
    }

    public function indexDraft()
    {
        $user_id = Auth::user()->id;
        $pengembalianBarang = PengembalianBarang::where('penerima_id', $user_id)->where('status', 'Draft')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Pengembalian Barang successfully retrieved!',
            'data' => $pengembalianBarang
        ], 200);
    }
    public function indexOnApproval()
    {
        $user_id = Auth::user()->id;
        $pengembalianBarang = PengembalianBarang::where('penerima_id', $user_id)->where('status', 'On Approval')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Pengembalian Barang successfully retrieved!',
            'data' => $pengembalianBarang
        ], 200);
    }
    public function indexDone()
    {
        $user_id = Auth::user()->id;
        $pengembalianBarang = PengembalianBarang::where('penerima_id', $user_id)->where('status', 'Done')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Pengembalian Barang successfully retrieved!',
            'data' => $pengembalianBarang
        ], 200);
    }

    public function show($id)
    {
        $pengembalianBarang = PengembalianBarang::find($id);

        if ($pengembalianBarang == null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengembalian Barang not found!',
                'data' => $pengembalianBarang
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Pengembalian Barang retrieved successfully!',
                'data' => $pengembalianBarang
            ], 200);
        }
    }

    public function create( Request $request)
    {
        $penerima = Auth::user()->name;
        $penerima_id = Auth::user()->id;

        $formattedDate = Carbon::now()->format('Y-m-d');

        $pengembalianBarang = PengembalianBarang::create([
            'status'  => 'Draft',
            'surat_jalan_id'  => $request->surat_jalan_id,
            'no_surat_jalan'  => $request->no_surat_jalan,
            'penerima'  => $penerima, 
            'penerima_id'  => $penerima_id, 
            'penerima_date'  => $formattedDate,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian Barang successfully created!',
            'data' => $pengembalianBarang,
        ], 200);
    }

    public function update(Request $request, $id)
    {

        $pengembalianBarang = PengembalianBarang::find($id);

        if ($pengembalianBarang == null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengembalian Barang not found!',
                'data' => $pengembalianBarang
            ], 404);
        } else {
            if($pengembalianBarang->surat_jalan_id != $request->surat_jalan_id){
                $pengembalianBarangDetail = PengembalianBarangDetail::where('pengembalian_barang_id', $id)->get();
                foreach ($pengembalianBarangDetail as $item) {
                    $item->delete();
                }
            }
            $pengembalianBarang->update([
                'surat_jalan_id'  => $request->surat_jalan_id, 
                'no_surat_jalan'  => $request->no_surat_jalan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian Barang updated successfully!',
                'data' => $pengembalianBarang
            ], 200);
        }
    }

    public function destroy($id)
    {
        $pengembalianBarang = PengembalianBarang::find($id);

        if ($pengembalianBarang == null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengembalian Barang not found!',
                'data' => $pengembalianBarang
            ], 404);
        } else {
            $pengembalianBarang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian Barang deleted successfully!',
            ], 200);
        }
    }

    public function post($id)
    {

        $pengembalian_barang = PengembalianBarang::find($id);
        $pengembalian_barang_detail = PengembalianBarangDetail::where('pengembalian_barang_id', $id)->get();

        if ($pengembalian_barang == null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengembalian Barang not found!',
                'data' => $pengembalian_barang
            ], 404);
        }

        if (count($pengembalian_barang_detail) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Item Pengembalian Barang minimal 1 item!',
                'data' => $pengembalian_barang_detail
            ], 404);
        }

        if( $pengembalian_barang->surat_jalan_id == null){
            return response()->json([
                'success' => false,
                'message' => 'Mohon Pilih Surat Jalan terlebih dahulu!'
            ], 400);    
        
        }
        DB::beginTransaction();
        try {
            foreach($pengembalian_barang_detail as $item){
                if($item->quantity_dikembalikan == null){
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity dikembalikan harus diisi, (bila tidak ada yang dikembalikan, isi 0)',
                    ], 400);
                }
                
                if($item->quantity_dikirim < $item->quantity_dikembalikan){    
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity dikembalikan ('.$item->quantity_dikembalikan.') harus lebih kecil dari Quantity dikirim ('.$item->quantity_dikirim.')',
                        'data' => $item,
                    ], 400);
                }

                $stock_material = StockMaterial::find($item->stock_material_id);
                $stock_material->update([
                    'quantity' => $stock_material->quantity + $item->quantity_dikembalikan
                ]);
            }

            $pengembalian_barang->update([
                        'status'=>'Done',
                    ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian Barang updated successfully!',
                'data' => $pengembalian_barang
            ], 200);    

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e
            ], 400);
        }
    }
}
