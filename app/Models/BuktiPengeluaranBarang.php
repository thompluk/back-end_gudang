<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPengeluaranBarang extends Model
{
    use HasFactory;
    protected $table = 'bukti_pengeluaran_barang';

    protected $fillable = [
        'id',
        'status',
        'salesman',
        'date',
        'no_po',
        'delivery_by',
        'delivery_date',
        'no_bpb',
        'customer',
        'customer_address',
        'customer_pic_name',
        'customer_pic_phone',
        'request_by',
        'request_by_id',
        'request_by_date',
        'approved_by',
        'approved_by_id',
        'approved_by_date',
        'approved_by_status',
        'remarks',
        'created_at',
        'updated_at'
    ];

    public function buktiPengeluaranBarangDetail()
    {
        return $this->hasMany(BuktiPengeluaranBarangDetail::class, 'bpb_id', 'id');
    }
}
