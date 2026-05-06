<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSubImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'sort_order',
        'image_blob',
        'image_mime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getImageDataUriAttribute(): ?string
    {
        if (! $this->image_blob || ! $this->image_mime) {
            return null;
        }

        return 'data:' . $this->image_mime . ';base64,' . base64_encode($this->image_blob);
    }
}
