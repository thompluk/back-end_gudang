<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianBarangDetail extends Model
{
    use HasFactory;

    protected $table = 'pengembalian_barang_detail';

    protected $fillable = [
        'id',
        'pengembalian_barang_id',
        'surat_jalan_detail_id',
        'stock_material_id',
        'nama_barang',
        'quantity_dikirim',
        'quantity_dikembalikan',
        'keterangan',
        'created_at',
        'updated_at',
    ];

    public function pengembalianBarang(){
        return $this->belongsTo(PengembalianBarang::class, 'pengembalian_barang_id', 'id');
    }
}
