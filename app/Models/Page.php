<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    public const TEMPLATE_OPTIONS = [
        'page' => 'Default Page',
        'headline' => 'Headline',
        'feature_story' => 'Feature Story',
        'standard_news' => 'Standard News',
        'short_brief' => 'Short/Brief',
        'event' => 'Event',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_blob',
        'image_mime',
        'template',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getImageDataUriAttribute(): ?string
    {
        if (! $this->image_blob || ! $this->image_mime) {
            return null;
        }

        return 'data:' . $this->image_mime . ';base64,' . base64_encode($this->image_blob);
    }

    public function getTemplateLabelAttribute(): string
    {
        return self::TEMPLATE_OPTIONS[$this->template] ?? ucfirst(str_replace('_', ' ', (string) $this->template));
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
