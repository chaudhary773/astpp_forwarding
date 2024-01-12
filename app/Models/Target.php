<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    use HasFactory;

    protected $table = 'target';

    protected $guarded = [];
    public $timestamps = false;


    protected $casts = [
        'creationdate' => 'datetime',
        'modifieddate' => 'datetime',
        'active' => 'boolean',
    ];

    public function campaign() : BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public static function scopeAllTargets(Builder $query): void
    {
        $query->where('customerid', auth()->id());
    }

}
