<?php

declare(strict_types=1);

namespace Restatify\Shared\Util;

final class PrivacyLegalNotice {
    public const POLYLANG_GROUP = 'Restatify Shared';
    public const NOTICE_KEY = 'restatify_shared_legal_notice';
    public const LINK_LABEL_KEY = 'restatify_shared_privacy_policy_label';
    public const NOTICE_TEXT = 'Mit der Nutzung dieses Tools stimmst du unseren Datenschutzbestimmungen zu.';
    public const LINK_LABEL_TEXT = 'Datenschutzerklärung';

    public static function registerPolylangStrings(): void {
        if (!function_exists('pll_register_string') && !class_exists('\\Restatify\\Shared\\I18n\\PolylangAdapter', false)) {
            return;
        }

        self::register(self::NOTICE_KEY, self::NOTICE_TEXT, self::POLYLANG_GROUP, false);
        self::register(self::LINK_LABEL_KEY, self::LINK_LABEL_TEXT, self::POLYLANG_GROUP, false);
    }

    public static function renderDefault(string $privacyPolicyUrl, string $cssClass): string {
        return self::render(self::NOTICE_TEXT, $privacyPolicyUrl, self::LINK_LABEL_TEXT, $cssClass);
    }

    public static function render(string $noticeText, string $privacyPolicyUrl, string $linkLabel, string $cssClass): string {
        $url = trim($privacyPolicyUrl);
        if ($url === '') {
            return '';
        }

        $notice = self::translate($noticeText);
        $label = self::translate($linkLabel);

        return sprintf(
            '<p class="%s">%s <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>.</p>',
            esc_attr($cssClass),
            esc_html($notice),
            esc_url($url),
            esc_html($label)
        );
    }

    private static function translate(string $value): string {
        if ($value === '') {
            return '';
        }

        if (class_exists('\\Restatify\\Shared\\I18n\\PolylangAdapter', false)) {
            return \Restatify\Shared\I18n\PolylangAdapter::translate($value);
        }

        if (function_exists('pll__')) {
            $translated = pll__($value);
            if (is_string($translated) && $translated !== '') {
                return $translated;
            }
        }

        return $value;
    }

    private static function register(string $name, string $value, string $group, bool $multiline = false): void {
        if (class_exists('\\Restatify\\Shared\\I18n\\PolylangAdapter', false)) {
            \Restatify\Shared\I18n\PolylangAdapter::register($name, $value, $group, $multiline);
            return;
        }

        if ($value !== '' && function_exists('pll_register_string')) {
            pll_register_string($name, $value, $group, $multiline);
        }
    }
}
