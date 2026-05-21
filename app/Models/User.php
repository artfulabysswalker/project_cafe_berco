<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Achievement;
use App\Models\CartItem;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Referral;
use App\Models\Redemption;
use App\Models\Review;
use App\Models\Role;
use App\Models\Voucher;
use App\Models\PlaylistVote;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use Notifiable;


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
        'is_guest',
        'exp',
        'last_daily_claim',
        'referral_code',
        'referred_by',
        'referral_balance',
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
        'last_daily_claim' => 'datetime',
        'exp' => 'integer',
        'referral_balance' => 'integer',
        'is_guest' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_user';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'int';


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
        return 'id_user';
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
        return $this->hasMany(CartItem::class, 'user_id', 'id_user');
    }

    public function redemptions()
    {
        return $this->hasMany(Redemption::class, 'user_id', 'id_user');
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

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id_user');
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    public function orders() // Foreign key in orders table is 'id_user', local key in users table is 'id_user'
    {
        return $this->hasMany(Order::class, 'id_user', 'id_user');
    }

    public function favorites() // Assuming foreign key in favorites table is 'user_id', local key in users table is 'id_user'
    {
        return $this->hasMany(Favorite::class, 'user_id', 'id_user');
    }

    public function getCompletedOrdersCount()
    {
        return $this->orders()
            ->where('status_order', 'completed')
            ->count();
    }

    public function getTotalSpent()
    {
        return $this->orders()
            ->where('status', 'completed')
            ->sum('total');
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_id', 'id_user');
    }
    

public function playlistVotes()
{
    return $this->hasMany(PlaylistVote::class, 'user_id', 'id_user');
}
}
