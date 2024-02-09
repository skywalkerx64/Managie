<?php

namespace App\Rules;

use Closure;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;

class CanLogin implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::where('email', $value)->first();
        if($user != null)
        {
            if(($user->email_verified_at == null) || (!$user->can_login) || (!$user->is_active))
            {
                $fail("This email is not allowed to login");
            }
        }
    }
}
