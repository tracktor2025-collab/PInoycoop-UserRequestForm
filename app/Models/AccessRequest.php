<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    protected $fillable = [
        'request_number',
        'full_name',
        'email',
        'mobile_no',
        'coop_name_branch',
        'request_date',
        'status',
        'systems',
        'summary',
        'pdf_path',
        'approved_at',
        'approved_by',
        'approval_remarks',
    ];

    protected $casts = [
        'systems' => 'array',
        'summary' => 'array',
        'request_date' => 'date',
        'approved_at' => 'datetime',
    ];
}
