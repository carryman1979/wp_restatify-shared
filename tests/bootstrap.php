<?php

declare(strict_types=1);

$GLOBALS['restatify_shared_test_options'] = [];
$GLOBALS['restatify_shared_test_site_options'] = [];
$GLOBALS['restatify_shared_test_transients'] = [];
$GLOBALS['restatify_shared_test_is_multisite'] = false;

if (!function_exists('__')) {
    function __(string $text, string $domain = ''): string {
        return $text;
    }
}

if (!function_exists('wp_unslash')) {
    function wp_unslash($value) {
        return is_string($value) ? stripslashes($value) : $value;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field(string $text): string {
        return trim($text);
    }
}

if (!function_exists('get_option')) {
    function get_option(string $name, $default = false) {
        return $GLOBALS['restatify_shared_test_options'][$name] ?? $default;
    }
}

if (!function_exists('update_option')) {
    function update_option(string $name, $value): bool {
        $GLOBALS['restatify_shared_test_options'][$name] = $value;
        return true;
    }
}

if (!function_exists('get_site_option')) {
    function get_site_option(string $name, $default = false) {
        return $GLOBALS['restatify_shared_test_site_options'][$name] ?? $default;
    }
}

if (!function_exists('update_site_option')) {
    function update_site_option(string $name, $value): bool {
        $GLOBALS['restatify_shared_test_site_options'][$name] = $value;
        return true;
    }
}

if (!function_exists('is_multisite')) {
    function is_multisite(): bool {
        return (bool) ($GLOBALS['restatify_shared_test_is_multisite'] ?? false);
    }
}

if (!function_exists('set_transient')) {
    function set_transient(string $name, $value, int $expiration = 0): bool {
        $expiresAt = $expiration > 0 ? time() + $expiration : null;
        $GLOBALS['restatify_shared_test_transients'][$name] = [
            'value' => $value,
            'expires_at' => $expiresAt,
        ];

        return true;
    }
}

if (!function_exists('get_transient')) {
    function get_transient(string $name) {
        if (!isset($GLOBALS['restatify_shared_test_transients'][$name])) {
            return false;
        }

        $entry = $GLOBALS['restatify_shared_test_transients'][$name];
        $expiresAt = $entry['expires_at'] ?? null;

        if ($expiresAt !== null && $expiresAt < time()) {
            unset($GLOBALS['restatify_shared_test_transients'][$name]);
            return false;
        }

        return $entry['value'] ?? false;
    }
}

$root = dirname(__DIR__);

require_once $root . '/src/php/Contracts/BookingPrefillSchema.php';
require_once $root . '/src/php/Util/TokenReplacer.php';
require_once $root . '/src/php/Util/BookingContactMethodsResolver.php';
require_once $root . '/src/php/Util/BookingContactChannelProfiles.php';
require_once $root . '/src/php/Util/BookingContactChannels.php';
require_once $root . '/src/php/Runtime/RateLimiter.php';
require_once $root . '/src/php/Runtime/PluginState.php';
require_once $root . '/src/php/Runtime/BootstrapGuard.php';
