<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'file_id',
        'amount',
        'duration',
        'payment_intent_id',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
