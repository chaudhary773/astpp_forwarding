<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCdr extends Model
{
    use HasFactory;

    protected $table = 'daily_cdr';

    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
        'date' => 'datetime',
        'missed'=> 'boolean',
    ];


}
