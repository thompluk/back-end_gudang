<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianBarang extends Model
{
    use HasFactory;
    protected $table = 'pengembalian_barang';

    protected $fillable = [
        'id',
        'status',
        'surat_jalan_id',
        'no_surat_jalan',
        'penerima',
        'penerima_id',
        'penerima_date',
        'updated_at',
        'created_at',
    ];

    public function pengembalianBarangDetail()
    {
        return $this->hasMany(PengembalianBarangDetail::class, 'pengembalian_barang_id', 'id');
    }
}
