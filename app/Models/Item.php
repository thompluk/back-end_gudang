<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'item';

    protected $fillable = [
        'id',
        'stock_id',
        'item_name',
        'no_edp',
        'no_sn',
        'no_ppb',
        'no_po',
        'description',
        'unit_price',
        'remarks',
        'item_unit',
        'is_in_stock',
        'arrival_date',
        'leaving_date',
        'receiver',
        'receiver_id',
        'updated_at',
        'created_at',
    ];

    public function stockItem(){
        return $this->belongsTo(StockItem::class, 'stock_id', 'id');
    }
}
