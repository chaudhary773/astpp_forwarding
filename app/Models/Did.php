<?php

namespace App\Models;

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

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'accountid', 'id');
    }

//    public function campaigns(): HasMany
//    {
//        return $this->HasMany(Campaign::class, 'did_id', 'id');
//    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
