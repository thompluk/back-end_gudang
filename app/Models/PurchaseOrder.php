<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_order';

    protected $fillable = [
        'id',
        'tanggal',
        'no_po',
        'status',
        'vendor',
        'vendor_id',
        'ship_to',
        'ship_to_id',
        'terms',
        'ship_via',
        'expected_date',
        'currency',
        'sub_total',
        'discount',
        'freight_cost',
        'ppn',
        'total_order',
        'say',
        'description',
        'prepared_by',
        'prepared_by_id',
        'prepared_by_date',
        'verified_by',
        'verified_by_id',
        'verified_by_date',
        'verified_by_status',
        'approved_by',
        'approved_by_id',
        'approved_by_date',
        'approved_by_status',
        'remarks',
        'updated_at',
        'created_at',
    ];

    public function purchaseOrderDetail()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'po_id', 'id');
    }
}