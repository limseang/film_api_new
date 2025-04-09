<?php

namespace App\Auth;

use Laravel\Sanctum\Guard;
use Laravel\Sanctum\Sanctum;

class SanctumGuard extends Guard
{
    /**
     * Determine if the bearer token is in the correct format.
     *
     * @param  string|null  $token
     * @return bool
     */
    protected function isValidBearerToken(?string $token = null): bool
    {
        // Copy of the original method but with the proper nullable type hint
        if (! is_null($token) && str_contains($token, '|')) {
            $model = new Sanctum::$personalAccessTokenModel;

            if ($model->getKeyType() === 'int') {
                [$id, $token] = explode('|', $token, 2);

                return ctype_digit($id) && ! empty($token);
            }

            return ! empty($token);
        }

        return false;
    }
}
