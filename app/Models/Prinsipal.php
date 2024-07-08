<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prinsipal extends Model
{
    use HasFactory;

    protected $table = 'prinsipal';

    protected $fillable = [
        'id',
        'name',
        'telephone',
        'fax',
        'updated_at',
        'created_at',
    ];
}
