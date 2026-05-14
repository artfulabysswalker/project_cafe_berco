<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Referral extends Model
{
    protected $fillable = [
        'referral_code',
        'referrer_id',
        'referee_id',
        'status',
        'expires_at',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public static function generateReferralCode()
    {
        return strtoupper(Str::random(8));
    }
}