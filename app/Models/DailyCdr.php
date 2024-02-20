<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCdr extends Model
{
    use HasFactory;

    protected $table = 'daily_cdr';

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
        'date' => 'date',
        'missed'=> 'boolean',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class, 'buyerid');
    }
}
