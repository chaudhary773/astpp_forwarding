<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Did extends Model
{
    use HasFactory;

    protected $table = 'dids';

    protected $hidden = ['pivot'];
    protected  $guarded = [];

    const CREATED_AT = 'assign_date';
    const UPDATED_AT = 'last_modified_date';

    protected $casts = [
        'assign_date' => 'datetime',
        'last_modified_date' => 'datetime',
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'accountid', 'id');
    }

//    public function campaign(): BelongsTo
//    {
//        return $this->BelongsTo(Campaign::class, );
//    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_did', 'did_id', 'campaign_id');
    }

    public static function scopeAllDids(Builder $query): void
    {
        $query->where('accountid', auth()->id())->where('call_type', '=', 6);
    }


}
