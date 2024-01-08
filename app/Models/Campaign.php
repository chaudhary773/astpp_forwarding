<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaign';
    protected $primaryKey = 'id';

    protected $hidden = ['pivot'];

    protected $guarded = [];

    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'modified_date';

    public function accounts(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'customer_id', 'id');
    }

//    public function dids(): BelongsToMany
//    {
//        return $this->BelongsToMany(CampDid::class, 'did_assign')->withPivot('camp_id', 'did_id');
//    }


    public function did(): HasMany
    {
        return $this->HasMany(CampDid::class, 'did_id');
    }

    public function dids(): BelongsToMany
    {
        return $this->belongsToMany(Did::class);
    }

    public function target(): HasOne
    {
        return $this->hasOne(Target::class);
    }
}
