<?php

declare(strict_types=1);

namespace Restatify\Shared\I18n;

final class PolylangAdapter {
    public static function isAvailable(): bool {
        return function_exists('pll_register_string');
    }

    public static function register(string $name, string $value, string $group, bool $multiline = false): void {
        if ($value === '' || !function_exists('pll_register_string')) {
            return;
        }

        pll_register_string($name, $value, $group, $multiline);
    }

    public static function translate(string $value): string {
        if ($value === '' || !function_exists('pll__')) {
            return $value;
        }

        $translated = pll__($value);
        return is_string($translated) && $translated !== '' ? $translated : $value;
    }
}
