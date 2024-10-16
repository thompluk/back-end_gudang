<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'id',
        'name',
        'address',
        'telephone',
        'fax',
        'email',
        'updated_at',
        'created_at',
    ];
}
