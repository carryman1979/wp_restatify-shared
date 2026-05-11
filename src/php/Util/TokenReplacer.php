<?php

declare(strict_types=1);

namespace Restatify\Shared\Util;

final class TokenReplacer {
    /**
     * @param array<string,string> $replacements
     */
    public static function replace(string $template, array $replacements): string {
        if ($template === '' || count($replacements) === 0) {
            return $template;
        }

        $search = [];
        $replace = [];

        foreach ($replacements as $token => $value) {
            $normalizedToken = self::normalizeToken($token);
            $search[] = $normalizedToken;
            $replace[] = (string) $value;
        }

        return str_replace($search, $replace, $template);
    }

    /**
     * @param array<string,string> $replacements
     * @param array<string,string> $templates
     * @return array<string,string>
     */
    public static function replaceAll(array $templates, array $replacements): array {
        $result = [];
        foreach ($templates as $key => $template) {
            $result[$key] = self::replace((string) $template, $replacements);
        }

        return $result;
    }

    private static function normalizeToken(string $token): string {
        $token = trim($token);
        if ($token === '') {
            return '{}';
        }

        if ($token[0] === '{' && substr($token, -1) === '}') {
            return $token;
        }

        return '{' . $token . '}';
    }
}
