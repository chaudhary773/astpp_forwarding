<?php

namespace App\Models;


use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasName, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'accounts';
    protected $primaryKey = 'id';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'is_recording' => 'boolean',
    ];


    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return str_ends_with($this->number, 'admin') && $this->type == -1;
        }
        return $this->isActive();
    }

    public function isActive(): bool
    {
        return $this->getAttributeValue('status') == 0;
    }

    public function getFilamentName(): string
    {
        return $this->getAttributeValue('first_name');
    }

    public function campaigns(): HasMany
    {
      return  $this->hasMany(Campaign::class, 'customer_id', 'id');
    }

    public function dids(): HasMany
    {
        return  $this->hasMany(Did::class, 'accountid');
    }

    public function call_block(): HasMany
    {
        return  $this->hasMany(CallBlock::class, 'customer_id');
    }

}
