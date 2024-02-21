<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaign';
    protected $primaryKey = 'id';

    protected $hidden = ['pivot'];

    protected $guarded = [];

    public const CREATED_AT = 'create_date';
    public const UPDATED_AT = 'modified_date';

    public function accounts(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'customer_id', 'id');
    }


    public function did(): HasMany
    {
        return $this->HasMany(CampDid::class, 'did_id');
    }


    public function dids()
    {
        return $this->belongsToMany(Did::class, 'campaign_did', 'campaign_id', 'did_id');
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    public function liveCalls(): HasMany
    {
        return $this->hasMany(LiveCall::class, 'campid', 'id');
    }

    public function liveCallsCount():int
    {
        return $this->hasMany(LiveCall::class, 'campid', 'id')->count();
    }

}
