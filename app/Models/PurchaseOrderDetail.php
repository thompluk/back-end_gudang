<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_detail';

    protected $fillable = [
        'id',
        'po_id',
        'item',
        'no_ppb',
        'ppb_detail_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'amount',
        'remarks',
        'item_unit',
        'is_items_created',
        'updated_at',
        'created_at',
    ];

    public function purchaseOrder(){
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'id');
    }
}