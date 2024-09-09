<?php

namespace App\Http\Controllers;

use App\Models\Companies;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function indexpoumum()
    {
        $suratJalan = SuratJalan::orderBy('created_at')->where('status', 'Done')->get();
        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }

    public function indexDraft()
    {
        $suratJalan = SuratJalan::where('status', 'Draft')
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
        $suratJalan = SuratJalan::where('status', 'On Approval')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }
    public function indexDone()
    {
        $suratJalan = SuratJalan::where('status', 'Done')->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Surat Jalan successfully retrieved!',
            'data' => $suratJalan
        ], 200);
    }

    public function indexRejected()
    {
        $suratJalan = SuratJalan::where('status', 'Rejected')->orderBy('created_at')->get();

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

        if ($suratJalan == null) {
            return response()->json([
                'success' => false,
                'message' => 'Surat Jalan not found!',
                'data' => $suratJalan
                
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan retrieved successfully!',
                'data' => $suratJalan,
                'data2' => $company
            ], 200);
        }
    } 

    public function create( Request $request)
    {

        $menyerahkan = Auth::user()->name;
        $menyerahkan_id = Auth::user()->id;

        $formattedDate = Carbon::now()->format('Y-m-d');

        $suratJalan = SuratJalan::create([

            'status'  => 'Draft',  
            'status_pengiriman'  => null, 
            'company_id'  => $request->company_id, 
            'company'  => $request->company, 
            'menyerahkan_id'  => $menyerahkan_id, 
            'menyerahkan'  => $menyerahkan, 
            'menyerahkan_date'  => $formattedDate, 
            'mengetahui_id'  => $request->mengetahui_id, 
            'mengetahui'  => $request->mengetahui, 
            'mengetahui_status'  => null, 
            'mengetahui_date'  => null, 
            'menerima'  => null, 
            'menerima_date'  => null, 
            'remarks'  => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surat Jalan successfully created!',
            'data' => $suratJalan,
        ], 200);
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

}
