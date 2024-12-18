<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPembelianBarang extends Model
{
    use HasFactory;

    protected $table = 'permintaan_pembelian_barang';

    protected $fillable = [
        'no_ppb',
        'tanggal',
        'status',
        'pemohon',
        'pemohon_id',
        'mengetahui',
        'mengetahui_id',
        'mengetahui_status',
        'mengetahui_date',
        'menyetujui',
        'menyetujui_id',
        'menyetujui_status',
        'menyetujui_date',
        'purchasing',
        'purchasing_id',
        'purchasing_status',
        'purchasing_date',
        'remarks',
        'updated_at',
        'created_at',
    ];

    public function permintaanPembelianBarangDetail()
    {
        return $this->hasMany(PermintaanPembelianBarangDetail::class, 'ppb_id', 'id');
    }
}
