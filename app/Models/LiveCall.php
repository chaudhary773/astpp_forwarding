<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveCall extends Model
{
    use HasFactory;


    protected $table = 'active_calls';

    public $timestamps = false;

    protected $casts = [
        'start_time' => 'datetime',
        'answer_time' => 'datetime',
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
