<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\StockItem;
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

    public function indexpoumum()
    {
        $po = PurchaseOrder::orderBy('tanggal')->where('status','!=', 'Draft')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }

    public function indexpodelivery()
    {
        $userRole = Auth::user()->role;

        if ($userRole == 'WAREHOUSE') {
            $po = PurchaseOrder::orderBy('tanggal')->where('arrival_status', 'Awaiting Delivery')->get();
        } else {
            $po = [];
        }

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }

    public function indexDraft()
    {
        $user_id = Auth::user()->id;
        $po = PurchaseOrder::where('prepared_by_id', $user_id)->where('status', 'Draft')->orWhere('status', 'Returned')
                                        ->orderBy('tanggal')
                                        ->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }
    public function indexOnApproval()
    {
        $user_id = Auth::user()->id;
        $po = PurchaseOrder::where('status', 'On Approval')->where('prepared_by_id', $user_id)->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }
    public function indexDone()
    {
        $user_id = Auth::user()->id;
        $po = PurchaseOrder::where('status', 'Done')->where('prepared_by_id', $user_id)->orderBy('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'All Purchase Order successfully retrieved!',
            'data' => $po
        ], 200);
    }

    public function indexRejected()
    {
        $user_id = Auth::user()->id;
        $po = PurchaseOrder::where('status', 'Rejected')->where('prepared_by_id', $user_id)->orderBy('tanggal')->get();

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
            'arrival_date'=>null,
            'receiver'=>null,
            'receiver_id'=>null,
            'arrival_status'=>null,
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
            'verified_by_date'=>null,
            'approved_by_status'=>'Waiting for Confirmation',
            'approved_by_date'=>null,
            'remarks'=>null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order updated successfully!',
            'data' => $po
        ], 200);
        
    }

    public function arrivalData($id){
        // $po = PurchaseOrder::find($id);
        // $po_detail = PurchaseOrderDetail::where('po_id', $id)->where('is_items_created', 0)->get();
        // $detail_length = count($po_detail);

        $po_detail = PurchaseOrderDetail::find($id);
        $po = PurchaseOrder::find($po_detail->po_id);

        $formattedDate = Carbon::now()->format('Y-m-d');
        $receiver = Auth::user()->name;
        $receiver_id = Auth::user()->id;
        
        for($i=0; $i<$po_detail->quantity; $i++){
            $data[] = [
                'po_detail_id' => $po_detail->id,
                'item_name' => $po_detail->item,
                'no_po' => $po->no_po,
                'no_ppb' => $po_detail->no_ppb,
                'arrival_date' => $formattedDate,
                'description' => $po_detail->description,
                'quantity' => $po_detail->quantity,
                'unit_price' => $po_detail->unit_price,
                'remarks' => $po_detail->remarks,
                'item_unit' => $po_detail->item_unit,
                'arrival_date'=>$formattedDate,
                'receiver'=>$receiver,
                'receiver_id'=>$receiver_id,
                // 'detail_length' => $detail_length,
            ];    
        }

        // $data = [
        //     'arrival_date'=>$formattedDate,
        //     'receiver'=>$receiver,
        //     'receiver_id'=>$receiver_id,
        // ];



        return response()->json([
            'success' => true,
            'message' => 'Purchase Order updated successfully!',
            'data' => $data,
        ], 200);
    }

    public function arrived($id)
    {

        $po = PurchaseOrder::find($id);
        $po_detail = PurchaseOrderDetail::where('po_id', $id)->where('is_items_created', 0)->get();
        if(count($po_detail) != 0){
            return response()->json([
                'success' => false,
            ]);
        }

        $formattedDate = Carbon::now()->format('Y-m-d');
        $receiver = Auth::user()->name;
        $receiver_id = Auth::user()->id;

        if ($po == null) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase Order not found!',
                'data' => $po
            ], 404);
        } else {
            $po->update([
                'arrival_date'=>$formattedDate,
                'receiver'=>$receiver,
                'receiver_id'=>$receiver_id,
                'arrival_status'=>'Arrived',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order updated successfully!',
                'data' => $po,
            ], 200);
        }
    }

    public function indexDashboard()
    {
        // Mengambil status 'on approval' dan 'done', mengelompokkan berdasarkan bulan dari updated_at
        $po = PurchaseOrder::selectRaw('
                COUNT(*) as total,
                status,
                DATE_FORMAT(tanggal, "%m") as month
            ')
            ->whereIn('status', ['On Approval', 'Done']) // Memfilter status
            ->groupBy('month', 'status') // Mengelompokkan berdasarkan bulan numerik dan status
            ->orderBy('month', 'asc') // Mengurutkan berdasarkan bulan terbaru
            ->get();

        $datas = collect([]);

        foreach($po->groupBy('month') as $item){
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
}
