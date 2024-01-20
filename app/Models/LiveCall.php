<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\CallHangup;

class LiveCall extends Model
{
    use HasFactory;


    protected $table = 'active_calls';

    public $timestamps = false;

    protected $casts = [
        'start_time' => 'datetime',
        'answer_time' => 'datetime',
    ];
    protected array $dates = [
        'start_time',
        'answer_time',
    ];

    public function campaign() : BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campid', 'id');
    }
    public function target() : BelongsTo
    {
        return $this->belongsTo(Target::class, 'buyerid', 'id');
    }

    public function hangup(): void
    {
        CallHangup::hangup($this->call_id);
    }
}
