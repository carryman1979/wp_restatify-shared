<?php

declare(strict_types=1);

namespace Restatify\Shared\Security;

/**
 * Verifies server-issued CAPTCHA tokens with supported providers.
 */
final class CaptchaVerifier {
    /**
     * Verify a reCAPTCHA v3 or Cloudflare Turnstile token.
     */
    public static function verify(
        string $provider,
        string $secret,
        string $token,
        string $remoteIp = '',
        float $minimumScore = 0.5,
        string $expectedAction = ''
    ): bool {
        if ($secret === '' || $token === '') {
            return false;
        }

        if ($provider === 'recaptcha') {
            $endpoint = 'https://www.google.com/recaptcha/api/siteverify';
        } elseif ($provider === 'turnstile') {
            $endpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        } else {
            return false;
        }

        $requestBody = [
            'secret' => $secret,
            'response' => $token,
        ];
        if ($remoteIp !== '') {
            $requestBody['remoteip'] = $remoteIp;
        }

        $response = wp_remote_post($endpoint, [
            'body' => $requestBody,
            'timeout' => 10,
        ]);

        if (is_wp_error($response) || !is_array($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $result = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($result) || empty($result['success'])) {
            return false;
        }

        if ($expectedAction !== '' && ($result['action'] ?? '') !== $expectedAction) {
            return false;
        }

        if ($provider === 'recaptcha') {
            return isset($result['score'])
                && is_numeric($result['score'])
                && (float) $result['score'] >= $minimumScore;
        }

        return true;
    }
}
