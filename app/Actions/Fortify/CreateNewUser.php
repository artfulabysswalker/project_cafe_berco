<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $referralCode = Referral::generateReferralCode();
        $referredBy = null;

        // Check if user is registering with a referral code
        if (!empty($input['referral_code'])) {
            $referral = Referral::where('referral_code', $input['referral_code'])->first();
            if ($referral && $referral->status === 'pending') {
                $referredBy = $referral->referrer_id;
            }
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'referral_code' => $referralCode,
            'referred_by' => $referredBy,
        ]);

        // If user was referred, create referral record
        if ($referredBy) {
            $referrer = User::find($referredBy);
            Referral::create([
                'referrer_id' => $referrer->id,
                'referee_id' => $user->id,
                'referral_code' => $input['referral_code'],
                'reward_amount' => config('referral.reward_amount', 25000),
                'status' => 'pending',
                'expires_at' => now()->addDays(config('referral.expiry_days', 30)),
            ]);
        }

        return $user;
    }
}
