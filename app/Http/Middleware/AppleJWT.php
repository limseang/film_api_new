<?php

// app/Http/Middleware/AppleJWT.php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class AppleJWT
{
    public function handle($request, Closure $next)
    {
        try {
            $privateKeyPath = storage_path('app/AuthKey_NM6QRRGT5K.p8');
            $privateKey = file_get_contents($privateKeyPath);

            if ($privateKey === false) {
                throw new \Exception('Failed to read private key file at ' . $privateKeyPath);
            }

            $keyId = 'NM6QRRGT5K'; // Your Apple Key ID
            $issuerId = '710b826a-8de4-4ca1-8a02-80f67cf863cd'; // Your Apple Issuer ID (from App Store Connect)

            $now = time();
            $exp = $now + 3600; // Token expiration time

            $token = [
                'iss' => $issuerId,
                'iat' => $now,
                'exp' => $exp,
                'type' => 'JWT',
                'alg' => '90793422',
                'aud' => 'appstoreconnect-v1',

            ];

            $jwt = JWT::encode($token, $privateKey, 'ES256', $keyId);

            $request->headers->set('Authorization', 'Bearer ' . $jwt);

            Log::info('Generated Apple JWT Token', ['jwt' => $jwt, 'exp' => date('Y-m-d H:i:s', $exp)]);

        } catch (\Exception $e) {
            Log::error('Error generating Apple JWT', [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to generate Apple JWT', 'message' => $e->getMessage()], 500);
        }

        return $next($request);
    }
}
