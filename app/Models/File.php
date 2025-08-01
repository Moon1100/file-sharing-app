<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class File extends Model
{
    protected $fillable = [
        'uploader_token',
        'original_name',
        'compressed_path',
        'pin_code',
        'downloads',
        'max_downloads',
        'expires_at',
        'is_premium',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_premium' => 'boolean',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isDownloadLimitReached(): bool
    {
        return $this->downloads >= $this->max_downloads;
    }

    public function canBeDownloaded(): bool
    {
        return !$this->isExpired() && !$this->isDownloadLimitReached();
    }

    public function getRemainingTimeAttribute(): int
    {
        return max(0, Carbon::now()->diffInSeconds($this->expires_at, false));
    }

    public function getRemainingDownloadsAttribute(): int
    {
        return max(0, $this->max_downloads - $this->downloads);
    }
}
