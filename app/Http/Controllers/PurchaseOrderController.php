<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $po = PurchaseOrder::orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }

    public function indexDraft()
    {
        $po = PurchaseOrder::where('status', 'Draft')
                                        ->orWhere('status', 'Returned')
                                        ->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }
    public function indexOnApproval()
    {
        $po = PurchaseOrder::where('status', 'On Approval')->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }
    public function indexDone()
    {
        $po = PurchaseOrder::where('status', 'Done')->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }

    public function indexRejected()
    {
        $po = PurchaseOrder::where('status', 'Rejected')->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }

    public function show($id)
    {
        $po = PurchaseOrder::find($id);

        if ($po == null) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order not found!',
                'data' => $po
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order retrieved successfully!',
                'data' => $po
            ], 200);
        }
    } 

    public function create( Request $request)
    {
        $no_po = $this->generateNoPo();

        $prepared_by = Auth::user()->name;
        $prepared_by_id = Auth::user()->id;

        $formattedDate = Carbon::now()->format('Y-m-d');
        
        if ($request->discount == '') {
            $discount = 0;
        }else{
            $discount = $request->discount;
        }

        if ($request->freight_cost == '') {
            $freight_cost = 0;
        }else{
            $freight_cost = $request->freight_cost;
        }
        

        $po = PurchaseOrder::create([
            'no_po'=>$no_po,
            'tanggal'=>$formattedDate,
            'status' => 'Draft',
            'vendor'=>$request->vendor,
            'vendor_id'=>$request->vendor_id,
            'ship_to'=>$request->ship_to,
            'ship_to_id'=>$request->ship_to_id,
            'terms'=>$request->terms,
            'ship_via'=>$request->ship_via,
            'expected_date'=>$request->expected_date,
            'currency'=>$request->currency,
            'sub_total'=>$request->sub_total,
            'discount'=>$discount,
            'freight_cost'=>$freight_cost,
            'ppn'=>$request->ppn,
            'total_order'=>$request->total_order,
            'say'=>$request->say,
            'description'=>$request->description,
            'prepared_by'=>$prepared_by,
            'prepared_by_id'=>$prepared_by_id,
            'prepared_by_date'=>$formattedDate,
            'verified_by'=>$request->verified_by,
            'verified_by_id'=>$request->verified_by_id,
            'verified_by_date'=>null,
            'verified_by_status'=>null,
            'approved_by'=>$request->approved_by,
            'approved_by_id'=>$request->approved_by_id,
            'approved_by_date'=>null,
            'approved_by_status'=>null,
            'remarks'=>null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order successfully created!',
            'data' => $po,
        ], 200);
    }

    private function generateNoPo()
    {
    return DB::transaction(function () {
        $currentDate = Carbon::now();
        $year = $currentDate->year;
        $month = str_pad($currentDate->month, 2, '0', STR_PAD_LEFT);

        // Cari nomor terbesar yang ada saat ini
        $lastNoPo = PurchaseOrder::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('no_po', 'desc')
            ->value('no_po');

        // Jika ada nomor sebelumnya, increment nomor tersebut, jika tidak, mulai dari 1
        if ($lastNoPo) {
            $lastSequence = (int) substr($lastNoPo, -3);
            $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '001';
        }

        $newNoPo = 'po_'.$year . $month . $newSequence;

        return $newNoPo;
    });
    }

    public function update(Request $request, $id)
    {

        $po = PurchaseOrder::find($id);

        if ($request->discount == '') {
            $discount = 0;
        }else{
            $discount = $request->discount;
        }

        if ($request->freight_cost == '') {
            $freight_cost = 0;
        }else{
            $freight_cost = $request->freight_cost;
        }

        if ($po == null) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order not found!',
                'data' => $po
            ], 404);
        } else {
            $po->update([
                'vendor'=>$request->vendor,
                'vendor_id'=>$request->vendor_id,
                'ship_to'=>$request->ship_to,
                'ship_to_id'=>$request->ship_to_id,
                'terms'=>$request->terms,
                'ship_via'=>$request->ship_via,
                'expected_date'=>$request->expected_date,
                'currency'=>$request->currency,
                'sub_total'=>$request->sub_total,
                'discount'=>$discount,
                'freight_cost'=>$freight_cost,
                'ppn'=>$request->ppn,
                'total_order'=>$request->total_order,
                'say'=>$request->say,
                'description'=>$request->description,
                'verified_by'=>$request->verified_by,
                'verified_by_id'=>$request->verified_by_id,
                'approved_by'=>$request->approved_by,
                'approved_by_id'=>$request->approved_by_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order updated successfully!',
                'data' => $po
            ], 200);
        }
    }

    public function destroy($id)
    {
        $po = PurchaseOrder::find($id);
        // $po_detail = PurchaseOrderDetail::where('po_id', $id)->get();

        if ($po == null) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order not found!',
                'data' => $po
            ], 404);
        } else {
            $po->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pruchase Order deleted successfully!',
            ], 200);
        }
    }

    public function post( $id)
    {

        $po = PurchaseOrder::find($id);
        $po_detail = PurchaseOrderDetail::where('po_id', $id)->get();

        if ($po == null) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order not found!',
                'data' => $po
            ], 404);
        }

        if (count($po_detail) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Item Purchase Order minimal 1 item!',
                'data' => $po
            ], 404);
        }

        if( $po->vendor_id == null || $po->ship_to_id == null || $po->terms == null || $po->ship_via == null || $po->expected_date == null || $po->currency == null ||
            $po->sub_total == null || $po->discount == null || $po->freight_cost == null || $po->ppn == null || $po->total_order == null || $po->say == null || $po->description == null ||
            $po->verified_by_id == null || $po->approved_by_id == null){
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh ada data Purchase Order yang kosong'
            ], 400);
        }
        
        foreach($po_detail as $item){
            if($item->item == null || $item->no_ppb == null || $item->ppb_detail_id == null || $item->description == null || $item->quantity == null || $item->unit_price == null ||
                $item->discount === null || $item->amount == null || $item->item_unit == null){
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak boleh ada data Item yang kosong'
                ], 400);
            }
        }
        
        $po->update([
            'status'=>'On Approval',
            'verified_by_status'=>'Waiting for Confirmation',
            'approved_by_status'=>'Waiting for Confirmation',
            'remarks'=>null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order updated successfully!',
            'data' => $po
        ], 200);
        
    }
}