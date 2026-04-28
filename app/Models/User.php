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
        'exp', // Tambahkan 'exp' agar bisa diisi
        'last_daily_claim', // Tambahkan 'last_daily_claim'
        'is_admin',
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
            'exp' => 'integer', // Cast 'exp' sebagai integer
            'last_daily_claim' => 'datetime', // Cast 'last_daily_claim' sebagai datetime
            'is_admin' => 'boolean',
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
     * Get the redemptions for the user.
     */
    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
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
}
