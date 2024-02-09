<?php

namespace App\Rules;

use Closure;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;

class HasRoles implements ValidationRule
{
    public array $roles;
    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find(intval($value));

        if(!$user->HasRoles($this->roles))
        {
            $fail("This user doesn't have the required roles");
        }
    }
}
