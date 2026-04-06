<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRequest extends Model
{
    protected $fillable = [
        'source_request_id',
        'request_number',
        'full_name',
        'email',
        'mobile_no',
        'coop_name',
        'branch',
        'request_date',
        'status',
        'systems',
        'summary',
        'pdf_path',
        'approval_signed_path',
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

    public function sourceRequest(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_request_id');
    }
}
