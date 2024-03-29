<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function dailyCdrs(): HasMany
    {
        return $this->hasMany(DailyCdr::class, 'buyerid', 'id')->where('date', now()->toDateString());
    }

    public function campCdrs(): HasMany
    {
        $month = now()->format('Y-m');
        return $this->hasMany(CDR::class, 'buyerid', 'id')->where('month', $month) ;
    }

    public function liveCdrs(): HasMany
    {
        return $this->hasMany(LiveCall::class, 'buyerid', 'id');
    }


    public static function scopeAllTargets(Builder $query): void
    {
        $query->where('customerid', auth()->id())->orderBy('active', 'desc')->orderBy('id', 'desc');
    }

}
