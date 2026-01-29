<?php

if (!function_exists('TokenSSO')) {
    /**
     * Decode and validate SSO JWT token from akses.unila.ac.id
     *
     * @param string $jwt The JWT token
     * @return object|false Decoded payload or false on failure
     */
    function TokenSSO($jwt)
    {
        $secret = env('SSO_JWT_SECRET', 'secret');
        $tokenParts = explode('.', $jwt);

        if (count($tokenParts) !== 3) {
            return false;
        }

        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];
        $signature_provided = str_replace(' ', '+', $signature_provided);
        $payload_decoded = json_decode($payload);

        if (!is_object($payload_decoded)) {
            return false;
        }

        $required_claims = [
            'id_aplikasi', 'url_aplikasi', 'id_pengguna', 'username',
            'nm_pengguna', 'peran_pengguna', 'id_sdm_pengguna', 'id_pd_pengguna',
            'email', 'token_dibuat', 'token_kadarluwasa', 'asal_domain',
            'ip_address', 'sso'
        ];

        foreach ($required_claims as $req) {
            if (!property_exists($payload_decoded, $req)) {
                return false;
            }
        }

        $expiration = $payload_decoded->token_kadarluwasa;
        $is_token_expired = ($expiration - time()) < 0;
        if ($is_token_expired) {
            return false;
        }

        $base64_url_header = base64_encode($header);
        $base64_url_payload = base64_encode($payload);
        $signature = hash_hmac('SHA256', "$base64_url_header.$base64_url_payload", $secret, true);
        $base64_url_signature = base64_encode($signature);
        $is_signature_valid = ($base64_url_signature === $signature_provided);

        if (!$is_signature_valid) {
            return false;
        }

        return $payload_decoded;
    }
}

if (!function_exists('generateJWT')) {
    /**
     * Generate a JWT token for frontend authentication
     *
     * @param array $payload Data to encode in token
     * @param int $expiry Expiry time in seconds (default 24 hours)
     * @return string JWT token
     */
    function generateJWT(array $payload, int $expiry = 86400): string
    {
        $secret = env('JWT_SECRET', 'your-jwt-secret-key');

        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $payload['iat'] = time();
        $payload['exp'] = time() + $expiry;

        $base64Header = base64_encode($header);
        $base64Payload = base64_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$base64Header.$base64Payload", $secret, true);
        $base64Signature = base64_encode($signature);

        return "$base64Header.$base64Payload.$base64Signature";
    }
}

if (!function_exists('verifyJWT')) {
    /**
     * Verify and decode a JWT token
     *
     * @param string $token JWT token
     * @return object|false Decoded payload or false on failure
     */
    function verifyJWT(string $token)
    {
        $secret = env('JWT_SECRET', 'your-jwt-secret-key');
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        // Handle case where + was converted to space when passing token via URL
        $base64Signature = str_replace(' ', '+', $base64Signature);

        $signature = hash_hmac('SHA256', "$base64Header.$base64Payload", $secret, true);
        $expectedSignature = base64_encode($signature);

        if ($base64Signature !== $expectedSignature) {
            return false;
        }

        $payload = json_decode(base64_decode($base64Payload));

        if (!$payload || !isset($payload->exp) || $payload->exp < time()) {
            return false;
        }

        return $payload;
    }
}

if (!function_exists('guid')) {
    /**
     * Generate a UUID v4
     *
     * @return string UUID
     */
    function guid(): string
    {
        if (function_exists('com_create_guid')) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
