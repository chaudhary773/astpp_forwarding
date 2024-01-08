<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CampDid extends Pivot
{
    public $table = 'campaign_did';
    public $incrementing = true;

    protected $guarded = [];

    public function campaign():BelongsTo
    {
        return $this->BelongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function did():BelongsTo
    {
        return $this->BelongsTo(Did::class, 'did_id', 'id');
    }
}
