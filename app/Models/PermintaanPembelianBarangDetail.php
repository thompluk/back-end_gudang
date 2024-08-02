<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPembelianBarangDetail extends Model
{
    use HasFactory;

    protected $table = 'permintaan_pembelian_barang_detail';

    protected $fillable = [
        'id',
        'ppb_id',
        'nama_barang',
        'kode',
        'spesifikasi',
        'quantity',
        'expected_eta',
        'project_and_customer',
        'updated_at',
        'created_at',
    ];

    public function permintaanPembelianBarang(){
        return $this->belongsTo(PermintaanPembelianBarang::class, 'ppb_id', 'id');
    }
}
