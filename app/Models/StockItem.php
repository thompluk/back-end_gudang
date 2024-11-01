<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $table = 'stock_item';

    protected $fillable = [
        'id',
        'stock_name',   
        'tipe',
        'prinsipal',
        'prinsipal_id',
        'quantity',
        'updated_at',
        'created_at',
    ];

    public function item()
    {
        return $this->hasMany(Item::class, 'stock_id', 'id');
    }
}
