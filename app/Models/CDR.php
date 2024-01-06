<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CDR extends Model
{
    use HasFactory;

    protected $table = 'camp_cdr';

    public $timestamps = false;

    protected $casts = [
        'call_start' => 'datetime',
        'call_end' => 'datetime',
        'month' => 'date',
        'missed' => 'boolean',
    ];

    public function campaign() : BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campid', 'id');
    }
    public function target() : BelongsTo
    {
        return $this->belongsTo(Target::class, 'buyerid', 'id');
    }

}
