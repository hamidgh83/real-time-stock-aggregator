<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'timestamp',
    ];

    public function symbolDetails(): BelongsTo
    {
        return $this->belongsTo(StockSymbol::class, 'symbol', 'name');
    }
}
