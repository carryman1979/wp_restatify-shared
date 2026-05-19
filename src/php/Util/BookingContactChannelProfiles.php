<?php

declare(strict_types=1);

namespace Restatify\Shared\Util;

final class BookingContactChannelProfiles {
    /**
     * @return array<string,array{label:string,input_kind:string,placeholder:string,value_label:string,ics_template:string}>
     */
    public static function defaultProfiles(): array {
        return [
            'phone' => [
                'label' => 'Telefon',
                'input_kind' => 'tel',
                'placeholder' => '+49...',
                'value_label' => 'Telefonnummer',
                'ics_template' => 'Telefon: {value}',
            ],
            'whatsapp' => [
                'label' => 'WhatsApp',
                'input_kind' => 'tel',
                'placeholder' => '+49...',
                'value_label' => 'Handynummer',
                'ics_template' => 'WhatsApp: {value}',
            ],
            'teams' => [
                'label' => 'Microsoft Teams',
                'input_kind' => 'email',
                'placeholder' => 'name@example.com',
                'value_label' => 'E-Mail-Adresse',
                'ics_template' => 'Teams Kontakt: {value}',
            ],
            'zoom' => [
                'label' => 'Zoom',
                'input_kind' => 'email',
                'placeholder' => 'name@example.com',
                'value_label' => 'E-Mail-Adresse',
                'ics_template' => 'Zoom Kontakt: {value}',
            ],
            'google_meet' => [
                'label' => 'Google Meet',
                'input_kind' => 'email',
                'placeholder' => 'name@example.com',
                'value_label' => 'E-Mail-Adresse',
                'ics_template' => 'Google Meet Kontakt: {value}',
            ],
            'signal' => [
                'label' => 'Signal',
                'input_kind' => 'tel',
                'placeholder' => '+49...',
                'value_label' => 'Handynummer',
                'ics_template' => 'Signal: {value}',
            ],
        ];
    }

    /**
     * @param callable(string):string|null $translate
     * @return array<int,array{key:string,label:string,input_kind:string,placeholder:string,value_label:string,ics_template:string}>
     */
    public static function defaultRows(?callable $translate = null): array {
        $profiles = self::defaultProfiles();
        $methods = BookingContactMethodsResolver::defaultMethods();
        $rows = [];

        foreach ($methods as $method) {
            if (!isset($profiles[$method])) {
                continue;
            }

            $profile = $profiles[$method];
            $rows[] = [
                'key' => $method,
                'label' => self::translateValue((string) ($profile['label'] ?? $method), $translate),
                'input_kind' => self::normalizeInputKind((string) ($profile['input_kind'] ?? 'text')),
                'placeholder' => (string) ($profile['placeholder'] ?? ''),
                'value_label' => self::translateValue((string) ($profile['value_label'] ?? 'Kontaktdaten'), $translate),
                'ics_template' => self::translateValue((string) ($profile['ics_template'] ?? '{value}'), $translate),
            ];
        }

        return $rows;
    }

    /**
     * @param callable(string):string|null $translate
     */
    public static function defaultRaw(?callable $translate = null): string {
        $lines = [];
        foreach (self::defaultRows($translate) as $row) {
            $lines[] = implode('|', [
                (string) ($row['key'] ?? ''),
                (string) ($row['label'] ?? ''),
                (string) ($row['input_kind'] ?? 'text'),
                (string) ($row['placeholder'] ?? ''),
                (string) ($row['value_label'] ?? 'Kontaktdaten'),
                (string) ($row['ics_template'] ?? '{value}'),
            ]);
        }

        return implode("\n", $lines);
    }

    private static function normalizeInputKind(string $value): string {
        $value = BookingContactMethodsResolver::normalizeKey($value);
        return in_array($value, ['tel', 'email', 'url', 'text'], true) ? $value : 'text';
    }

    /**
     * @param callable(string):string|null $translate
     */
    private static function translateValue(string $value, ?callable $translate): string {
        if ($translate === null) {
            return $value;
        }

        $translated = $translate($value);
        return is_string($translated) && $translated !== '' ? $translated : $value;
    }
}
