<?php

declare(strict_types=1);

namespace Restatify\Shared\Util;

final class BookingContactMethodsResolver {
    /**
     * @param array<string,mixed> $options
     * @return array<int,string>
     */
    public static function methodsFromOptions(array $options): array {
        $methods = [];

        $channels = is_array($options['contact_channels'] ?? null) ? $options['contact_channels'] : [];
        foreach ($channels as $channel) {
            if (!is_array($channel)) {
                continue;
            }

            $key = self::normalizeKey((string) ($channel['key'] ?? ''));
            if ($key !== '') {
                $methods[] = $key;
            }
        }

        if (count($methods) === 0) {
            $raw = (string) ($options['contact_channels_raw'] ?? '');
            $methods = self::methodsFromRaw($raw);
        }

        if (count($methods) === 0) {
            $methods = self::defaultMethods();
        }

        return self::uniqueNonEmpty($methods);
    }

    /**
     * @return array<int,string>
     */
    public static function methodsFromRaw(string $raw): array {
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        if (!is_array($lines)) {
            return [];
        }

        $methods = [];
        foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', (string) $line));
            $key = self::normalizeKey((string) ($parts[0] ?? ''));
            if ($key !== '') {
                $methods[] = $key;
            }
        }

        return self::uniqueNonEmpty($methods);
    }

    /**
     * @return array<int,string>
     */
    public static function defaultMethods(): array {
        $profiles_class = '\\Restatify\\Shared\\Util\\BookingContactChannelProfiles';
        $available_methods = [];
        if (class_exists($profiles_class)) {
            /** @var array<string,mixed> $profiles */
            $profiles = $profiles_class::defaultProfiles();
            $available_methods = array_keys($profiles);
        }

        if (count($available_methods) === 0) {
            $available_methods = ['phone', 'whatsapp', 'teams', 'zoom', 'google_meet', 'signal'];
        }

        $available_methods = self::uniqueNonEmpty($available_methods);

        if (class_exists('\\Restatify\\Shared\\Contracts\\BookingPrefillSchema', false)) {
            /** @var array<int,string> $schema_methods */
            $schema_methods = \Restatify\Shared\Contracts\BookingPrefillSchema::defaultContactMethods();
            $schema_methods = self::uniqueNonEmpty($schema_methods);

            $ordered = [];
            foreach ($schema_methods as $method) {
                if (in_array($method, $available_methods, true)) {
                    $ordered[] = $method;
                }
            }

            if (count($ordered) > 0) {
                return $ordered;
            }
        }

        return $available_methods;
    }

    public static function normalizeKey(string $value): string {
        if (function_exists('sanitize_key')) {
            return sanitize_key($value);
        }

        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_\-]/', '', $value);
        return is_string($value) ? $value : '';
    }

    /**
     * @param array<int,string> $values
     * @return array<int,string>
     */
    private static function uniqueNonEmpty(array $values): array {
        $out = [];
        foreach ($values as $value) {
            $key = self::normalizeKey((string) $value);
            if ($key !== '' && !in_array($key, $out, true)) {
                $out[] = $key;
            }
        }

        return $out;
    }
}
