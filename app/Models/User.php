<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\CartItem;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\Voucher;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use Notifiable;
    /*
    |--------------------------------------------------------------------------
    | Custom Primary Key
    |--------------------------------------------------------------------------
    */
    protected $primaryKey = 'id_user';

    public $incrementing = true;

    protected $keyType = 'int';


    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'id_role',
    ];


    /*
    |--------------------------------------------------------------------------
    | Hidden Fields
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];


    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /*
    |--------------------------------------------------------------------------
    | Role Relationship
    |--------------------------------------------------------------------------
    */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }


    /*
    |--------------------------------------------------------------------------
    | Login uses username instead of email
    |--------------------------------------------------------------------------
    */
    public function getAuthIdentifierName()
    {
        return 'username';
    }


    /*
    |--------------------------------------------------------------------------
    | User Initials
    |--------------------------------------------------------------------------
    */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }


    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class)
            ->withPivot([
                'status',
                'notified_at',
                'used_at'
            ])
            ->withTimestamps();
    }
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('earned_at')
            ->withTimestamps();
    }
    public function getCompletedOrdersCount()
    {
        return $this->orders()
            ->where('status', 'completed')
            ->count();
    }

    public function getTotalSpent()
    {
        return $this->orders()
            ->where('status', 'completed')
            ->sum('total_price');
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }
    

public function playlistVotes()
{
    return $this->hasMany(PlaylistVote::class);
}
}

