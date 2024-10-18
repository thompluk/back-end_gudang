<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;
    protected $table = 'surat_jalan';

    protected $fillable = [
        'id',
        'no_surat_jalan',
        'status',
        'company_id',
        'company',
        'menyerahkan_id',
        'menyerahkan',
        'menyerahkan_date',
        'mengetahui_id',
        'mengetahui',
        'mengetahui_status',
        'mengetahui_date',
        'remarks',
        'updated_at',
        'created_at',
    ];

    public function suratJalanDetail()
    {
        return $this->hasMany(SuratJalanDetail::class, 'surat_jalan_id', 'id');
    }
}
