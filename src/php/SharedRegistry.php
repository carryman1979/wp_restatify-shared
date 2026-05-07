<?php

declare(strict_types=1);

namespace Restatify\Shared;

/**
 * Runtime registry for versioned shared components.
 *
 * Plugins should request an exact version. Sharing is only allowed
 * when that exact version was already registered.
 */
final class SharedRegistry {
    /**
     * @var array<string,array<string,mixed>>
     */
    private static array $instances = [];

    /**
     * Registers component payload for an exact semantic version.
     *
     * @param array<string,mixed> $payload
     */
    public static function register(string $component, string $version, array $payload): void {
        $key = self::key($component, $version);
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = $payload;
        }
    }

    /**
     * @return array<string,mixed>|null
     */
    public static function get(string $component, string $version): ?array {
        $key = self::key($component, $version);
        return self::$instances[$key] ?? null;
    }

    public static function has(string $component, string $version): bool {
        $key = self::key($component, $version);
        return isset(self::$instances[$key]);
    }

    private static function key(string $component, string $version): string {
        return strtolower(trim($component)) . '@' . trim($version);
    }
}
