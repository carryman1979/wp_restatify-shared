<?php

declare(strict_types=1);

namespace Restatify\Shared\Runtime;

final class RateLimiter {
    public static function getClientIp(): string {
        $forwarded = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? (string) wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']) : '';
        if ($forwarded !== '') {
            $parts = array_map('trim', explode(',', $forwarded));
            foreach ($parts as $part) {
                if (filter_var($part, FILTER_VALIDATE_IP)) {
                    return $part;
                }
            }
        }

        $remote = isset($_SERVER['REMOTE_ADDR']) ? (string) wp_unslash($_SERVER['REMOTE_ADDR']) : '';
        if ($remote !== '' && filter_var($remote, FILTER_VALIDATE_IP)) {
            return $remote;
        }

        return 'unknown';
    }

    public static function hit(string $prefix, string $action, int $windowSeconds, int $maxRequests): bool {
        $window = max(10, min(3600, $windowSeconds));
        $max = max(1, min(1000, $maxRequests));

        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field((string) wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
        $fingerprint = md5(self::getClientIp() . '|' . $ua . '|' . $action);
        $key = $prefix . $fingerprint;

        $bucket = get_transient($key);
        if (!is_array($bucket)) {
            $bucket = ['count' => 0, 'start' => time()];
        }

        $now = time();
        $start = (int) ($bucket['start'] ?? $now);
        if (($now - $start) >= $window) {
            $bucket = ['count' => 0, 'start' => $now];
        }

        $bucket['count'] = (int) ($bucket['count'] ?? 0) + 1;
        set_transient($key, $bucket, $window);

        return ((int) $bucket['count']) <= $max;
    }
}
