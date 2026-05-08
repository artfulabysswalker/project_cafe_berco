<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'referral_code',
        'referred_by',
        'referral_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get all orders by this user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all cart items for this user
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get all referrals made by this user
     */
    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get all referrals received by this user
     */
    public function referralsReceived()
    {
        return $this->hasMany(Referral::class, 'referee_id');
    }

    /**
     * Get the user who referred this user
     */
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Get all achievements earned by this user
     */
    public function achievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * Get count of completed orders
     */
    public function getCompletedOrdersCount(): int
    {
        return $this->orders()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get total spent by this user
     */
    public function getTotalSpent(): float
    {
        return (float) $this->orders()
            ->where('status', 'completed')
            ->sum('total');
    }
}
