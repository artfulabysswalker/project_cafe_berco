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
        'phone',
        'notification_enabled',
        'role',
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
            'last_visit_at' => 'datetime',
            'is_active' => 'boolean',
            'notification_enabled' => 'boolean',
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
     * Get all vouchers for this user
     */
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers')
            ->withPivot('assigned_at', 'used_at', 'is_used')
            ->withTimestamps();
    }

    /**
     * Get all notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if user is inactive (hasn't visited for more than 30 days)
     */
    public function isInactive(): bool
    {
        if (!$this->last_visit_at) {
            return false;
        }

        return now()->diffInDays($this->last_visit_at) > 30;
    }

    /**
     * Update user's last visit time
     */
    public function updateLastVisit(): void
    {
        $this->update(['last_visit_at' => now()]);
    }

    /**
     * Get days since last visit
     */
    public function daysSinceLastVisit(): ?int
    {
        if (!$this->last_visit_at) {
            return null;
        }

        return now()->diffInDays($this->last_visit_at);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
