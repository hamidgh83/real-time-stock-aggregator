<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockSymbol extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'type',
        'market_open',
        'market_close',
        'currency',
        'region',
        'timezone',
    ];

    public function stockPrices(): HasMany
    {
        return $this->hasMany(StockPrice::class, 'symbol', 'name');
    }
}
