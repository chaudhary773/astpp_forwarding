<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallBlock extends Model
{
    use HasFactory;

    protected $table = 'call_block';
    protected $primaryKey = 'id';
    protected $guarded = [];
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'customer_id', 'id');
    }

}
