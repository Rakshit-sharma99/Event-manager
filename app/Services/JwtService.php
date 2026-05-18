<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class JwtService
{
    public function issue(User $user): string
    {
        $header = $this->encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = $this->encode([
            'sub' => (string) $user->getKey(),
            'role' => $user->role,
            'iat' => time(),
            'exp' => now()->addDays(7)->timestamp,
        ]);

        return $header.'.'.$payload.'.'.$this->sign($header.'.'.$payload);
    }

    public function verify(?string $token): bool
    {
        if (! $token || substr_count($token, '.') !== 2) {
            return false;
        }

        [$header, $payload, $signature] = explode('.', $token);
        $expected = $this->sign($header.'.'.$payload);

        if (! hash_equals($expected, $signature)) {
            return false;
        }

        $claims = json_decode(base64_decode(Str::of($payload)->replace(['-', '_'], ['+', '/'])), true);

        return is_array($claims) && ($claims['exp'] ?? 0) > time();
    }

    private function encode(array $data): string
    {
        return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
    }

    private function sign(string $value): string
    {
        return rtrim(strtr(base64_encode(hash_hmac('sha256', $value, config('app.key'), true)), '+/', '-_'), '=');
    }
}
