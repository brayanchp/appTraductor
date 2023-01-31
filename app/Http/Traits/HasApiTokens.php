<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens as BaseHasApiTokens;
use Laravel\Sanctum\NewAccessToken;

trait HasApiTokens
{
    use BaseHasApiTokens {
        BaseHasApiTokens::createToken as base_createToken;
    }

    public function createToken(string $name, array $abilities = ['*'])
    {
        $time = env('TIME_SESSION') ? env('TIME_SESSION') : 20;
        $expiresAt = now()->addMinutes($time);
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
            'token_expires_at' => $expiresAt,
        ]);

        return new NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
    }
}
