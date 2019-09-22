<?php

namespace App\Auth;

use App\Models\User;

(\Dotenv\Dotenv::create(__DIR__ . '/../../'))->load();

class Jwt
{
    /**
     * @return string: Generates jwt token
     */
    public static function getToken (array $payload, $expires_after = 28800)
    {
        $issue_time = time();
        $expires_at = $expires_after == 0 ? $expires_at = $issue_time + 31536000 : $issue_time + $expires_after;
        $payload = json_encode(array_merge($payload, [
            'issue_time' => $issue_time,
            'expires_at' => $expires_at
        ]));
        $key = getenv('APP_KEY');

        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        return $token = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * @param string $text
     * @return mixed
     */
    private static function base64UrlEncode (string $text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    public static function verify (string $token)
    {
        //return false if token contains no 'Bearer '
        if (strpos($token, ' ') === false) return false;

        $without_bearer = explode(' ', $token);
        $tokenParts = explode('.', $without_bearer[1]);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        //verifying if signature is correct
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, getenv('APP_KEY'), true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        //return false if signature is tampered
        if ($base64UrlSignature !== $signatureProvided) return false;

        $payload = json_decode($payload, true);
        $issue_time = $payload['issue_time'];
        $expires_at = $payload['expires_at'];
        $current_time = time();

        //return false if token is invalid
        if ($issue_time > $current_time || $current_time > $expires_at) return false;

        $user = User::where('email', $payload['email'])->first();

        //return false if user does not exist
        if (!$user) return false;

        return true;
    }

    public static function getApiTokenUser ($token)
    {
        $without_bearer = explode(' ', $token);
        $token_parts = $without_bearer[1];

        $payload = json_decode(base64_decode($token_parts[1]), true);
        $user = User::where('email', $payload['email'])->first();

        if (!$user) return false;

        return [
            'id' => $user->id,
            'email' => $user->email
        ];
    }

}
