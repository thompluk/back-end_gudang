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
        'stock_id',
        'stock_name',
        'quantity',
        'notes',
        'updated_at',
        'created_at',
    ];

    public function buktiPengeluaranBarang(){
        return $this->belongsTo(BuktiPengeluaranBarang::class, 'bpb_id', 'id');
    }

    public function buktiPengeluaranBarangDetailDetail()
    {
        return $this->hasMany(BuktiPengeluaranBarangDetailDetail::class, 'bpb_detail_id', 'id');
    }
}
