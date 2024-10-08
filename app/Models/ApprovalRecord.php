<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRecord extends Model
{
    use HasFactory;

    protected $table = 'approval_record';

    protected $fillable = [
        'id',
        'no',
        'date',
        'type',
        'requestor',
        'requestor_id',
        'approver',
        'approver_id',
        'action',
        'remarks',
        'updated_at',
        'created_at',
    ];


}
