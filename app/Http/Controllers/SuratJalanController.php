<?php

namespace App\Http\Controllers;

use App\Models\Companies;
use App\Models\StockMaterial;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuratJalanController extends Controller
{
    public function index()
    {
        $suratJalan = SuratJalan::orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }

    public function suratjalanselect()
    {
        // $suratJalan = SuratJalan::where('')->orderBy('created_at')->get();

        $suratJalan = SuratJalan::
            where('status', 'Done')
            ->whereNotIn('id', function($query) {
                $query->select('surat_jalan_id')->where('surat_jalan_id', '!=', null)->where('status', 'Done')
                    ->from('pengembalian_barang');
            })
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }

    public function indexsuratjalanumum()
    {
        $surat_jalan = SuratJalan::orderBy('menyerahkan_date')->where('status','!=', 'Draft')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $surat_jalan
        ], 200);
    }

    public function indexDraft()
    {
        $user_id = Auth::user()->id;
        $suratJalan = SuratJalan::where('menyerahkan_id', $user_id)->where('status', 'Draft')
                                        ->orWhere('status', 'Returned')
                                        ->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }
    public function indexOnApproval()
    {
        $user_id = Auth::user()->id;
        $suratJalan = SuratJalan::where('menyerahkan_id', $user_id)->where('status', 'On Approval')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }
    public function indexDone()
    {
        $user_id = Auth::user()->id;
        $suratJalan = SuratJalan::where('menyerahkan_id', $user_id)->where('status', 'Done')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }

    public function indexRejected()
    {
        $user_id = Auth::user()->id;
        $suratJalan = SuratJalan::where('menyerahkan_id', $user_id)->where('status', 'Rejected')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }

    public function show($id)
    {
        $suratJalan = SuratJalan::find($id);
        $company = Companies::find($suratJalan->company_id);

        $data= [

            'status'=> $suratJalan->status,
            'no_surat_jalan'=> $suratJalan->no_surat_jalan,
            'company_id'=> $suratJalan->company_id,
            'company'=> $suratJalan->company,
            'company_address'=> $company->address??'',
            'company_telephone'=> $company->telephone??'',
            'company_fax'=> $company->fax??'',
            'company_email'=> $company->email??'',
            'menyerahkan_id'=> $suratJalan->menyerahkan_id,
            'menyerahkan'=> $suratJalan->menyerahkan,
            'menyerahkan_date'=> $suratJalan->menyerahkan_date,
            'mengetahui_id'=> $suratJalan->mengetahui_id,
            'mengetahui'=> $suratJalan->mengetahui,
            'mengetahui_status'=> $suratJalan->mengetahui_status,
            'mengetahui_date'=> $suratJalan->mengetahui_date,
            'remarks'=> $suratJalan->remarks,

        ];

        if ($suratJalan == null) {
            return response()->json([
                'success' => false,
                'message' => 'Surat Jalan not found!',
                
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan retrieved successfully!',
                'data' => $data,
            ], 200);
        }
    } 

    public function create( Request $request)
    {

        $no_surat_jalan = $this->generateNoSuratJalan();
        $menyerahkan = Auth::user()->name;
        $menyerahkan_id = Auth::user()->id;

        $formattedDate = Carbon::now()->format('Y-m-d');

        $suratJalan = SuratJalan::create([
            'no_surat_jalan'  => $no_surat_jalan,
            'status'  => 'Draft',  
            'company_id'  => $request->company_id, 
            'company'  => $request->company, 
            'menyerahkan_id'  => $menyerahkan_id, 
            'menyerahkan'  => $menyerahkan, 
            'menyerahkan_date'  => $formattedDate, 
            'mengetahui_id'  => $request->mengetahui_id, 
            'mengetahui'  => $request->mengetahui, 
            'mengetahui_status'  => null, 
            'mengetahui_date'  => null, 
            'remarks'  => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat Jalan successfully created!',
            'data' => $suratJalan,
        ], 200);
    }

    private function generateNoSuratJalan()
    {
    return DB::transaction(function () {
        $currentDate = Carbon::now();
        $year = $currentDate->year;
        $month = str_pad($currentDate->month, 2, '0', STR_PAD_LEFT);

        // Cari nomor terbesar yang ada saat ini
        $lastNoSuratJalan = SuratJalan::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('no_surat_jalan', 'desc')
            ->value('no_surat_jalan');

        // Jika ada nomor sebelumnya, increment nomor tersebut, jika tidak, mulai dari 1
        if ($lastNoSuratJalan) {
            $lastSequence = (int) substr($lastNoSuratJalan, -3);
            $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '001';
        }

        $newNoSuratJalan = 'sj_'.$year . $month . $newSequence;

        return $newNoSuratJalan;
    });
    }

    public function update(Request $request, $id)
    {

        $suratJalan = SuratJalan::find($id);

        if ($suratJalan == null) {
            return response()->json([
                'success' => false,
                'message' => 'Surat Jalan not found!',
                'data' => $suratJalan
            ], 404);
        } else {
            $suratJalan->update([
                'company_id'  => $request->company_id, 
                'company'  => $request->company,
                'mengetahui_id'  => $request->mengetahui_id, 
                'mengetahui'  => $request->mengetahui, 
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan updated successfully!',
                'data' => $suratJalan
            ], 200);
        }
    }

    public function destroy($id)
    {
        $suratJalan = SuratJalan::find($id);
        // $suratJalanDetail = SuratJalanDetail::where('ppb_id', $id)->get();

        if ($suratJalan == null) {
            return response()->json([
                'success' => false,
                'message' => 'Surat Jalan not found!',
                'data' => $suratJalan
            ], 404);
        } else {
            $suratJalan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan deleted successfully!',
            ], 200);
        }
    }

    public function post($id)
    {

        $surat_jalan = SuratJalan::find($id);
        $surat_jalan_detail = SuratJalanDetail::where('surat_jalan_id', $id)->get();

        if ($surat_jalan == null) {
            return response()->json([
                'success' => false,
                'message' => 'Surat Jalan not found!',
                'data' => $surat_jalan
            ], 404);
        }

        if (count($surat_jalan_detail) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Item Surat Jalan minimal 1 item!',
                'data' => $surat_jalan_detail
            ], 404);
        }

        if( $surat_jalan->company_id == null || $surat_jalan->mengetahui_id == null){
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh ada data Surat Jalan yang kosong'
            ], 400);
        
        }

        foreach($surat_jalan_detail as $item){
            
            if($item->stock_material_id == null || $item->quantity == null){
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak boleh ada data Item yang kosong'
                ], 400);
            }
            

            $stock = StockMaterial::find($item->stock_material_id);
            if($stock->quantity < $item->quantity){    
                return response()->json([
                    'success' => false,
                    'message' => 'Stok item '.$stock->stock_name.' di gudang hanya tersedia '.$stock->quantity,
                    'data' => $stock,
                ], 400);
            }
        }

    
        $surat_jalan->update([
            'status'=>'On Approval',
            'mengetahui_status'=>'Waiting for Confirmation',
            'mengetahui_date'=>null,
            'remarks'=>null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat Jalan updated successfully!',
            'data' => $surat_jalan
        ], 200);
    }

}
