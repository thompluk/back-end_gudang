<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMaterial extends Model
{
    use HasFactory;

    protected $table = 'stock_material';

    protected $fillable = [
        'id',
        'stock_name',
        'quantity',
        'no_ppb',
        'no_po',
        'description',
        'unit_price',
        'remarks',
        'item_unit',
        'arrival_date',
        'receiver',
        'receiver_id',
        'updated_at',
        'created_at',
    ];
        
}
