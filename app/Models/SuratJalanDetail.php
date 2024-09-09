<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanDetail extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan_detail';

    protected $fillable = [
        'id',
        'surat_jalan_id',
        'stock_item_id',
        // 'no_edp',
        // 'no_sn',
        'nama_barang',
        'quantity',
        'is_dikembalikan',
        'keterangan',
        'created_at',
        'updated_at',
    ];

    public function suratJalan(){
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id', 'id');
    }
}
