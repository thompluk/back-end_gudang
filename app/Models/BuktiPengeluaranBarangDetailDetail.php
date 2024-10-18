<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPengeluaranBarangDetailDetail extends Model
{
    use HasFactory;

    protected $table = 'bukti_pengeluaran_barang_detail_detail';

    protected $fillable = [
        'id',
        'bpb_id',
        'bpb_detail_id',
        'item_id',
        'item_name',
        'no_edp',
        'no_sn',
        'notes',
        'updated_at',
        'created_at',
    ];

    public function buktiPengeluaranBarang(){
        return $this->belongsTo(BuktiPengeluaranBarang::class, 'bpb_id', 'id');
    }

    public function buktiPengeluaranBarangDetail(){
        return $this->belongsTo(BuktiPengeluaranBarangDetail::class, 'bpb_detail_id', 'id');
    }
}
