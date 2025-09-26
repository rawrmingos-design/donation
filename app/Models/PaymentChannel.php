<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'code',
        'name',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(PaymentProvider::class, 'provider_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'channel_id');
    }
}
