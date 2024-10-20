<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PriceChangesCollection extends ResourceCollection
{
    public $collects = PriceChangesResource::class;
}
