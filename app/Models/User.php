<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'status',
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

    /**
     * Get the id attribute alias for id_user.
     */
    public function getIdAttribute()
    {
        return $this->attributes['id_user'] ?? null;
    }

    /**
     * Set the id attribute alias for id_user.
     */
    public function setIdAttribute($value): void
    {
        $this->attributes['id_user'] = $value;
    }

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
            ->map(fn ($word) => Str::substr($word, 0, 1))
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
                'used_at',
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

    /**
     * Shift yang sedang aktif/open oleh user
     */
    public function activeShift()
    {
        return $this->hasOne(CashierShift::class, 'user_id', 'id_user')->where('status', 'open');
    }

    /**
     * Riwayat seluruh shift user
     */
    public function shifts()
    {
        return $this->hasMany(CashierShift::class, 'user_id', 'id_user');
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role && in_array(strtolower($this->role->role_name), ['admin', 'owner']);
    }

    /**
     * Check if user is cashier
     */
    public function isCashier(): bool
    {
        return $this->role && in_array(strtolower($this->role->role_name), ['cashier', 'kasir']);
    }

    /**
     * Check if user is staff / operational employee
     */
    public function isStaff(): bool
    {
        return $this->role && in_array(strtolower($this->role->role_name), ['staff', 'cashier', 'kasir', 'pegawai']);
    }

    /**
     * Check if user can manage menu and inventory
     */
    public function canManageMenu(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role && in_array(strtolower($this->role->role_name), ['customer', 'pelanggan']);
    }

    /**
     * Check if user is guest
     */
    public function isGuest(): bool
    {
        return $this->role && strtolower($this->role->role_name) === 'guest';
    }
}
