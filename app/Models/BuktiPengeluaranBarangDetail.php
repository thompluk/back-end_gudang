<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPengeluaranBarangDetail extends Model
{
    use HasFactory;

    protected $table = 'bukti_pengeluaran_barang_detail';

    protected $fillable = [
        'id',
        'bpb_id',
        'item_id',
        'item_name',
        'no_edp',
        'no_sn',
        'quantity',
        'delivery_date',
        'notes',
        'is_partial_delivery',
        'is_delivered',
        'updated_at',
        'created_at',
    ];

    public function buktiPengeluaranBarang(){
        return $this->belongsTo(BuktiPengeluaranBarang::class, 'bpb_id', 'id');
    }
}
