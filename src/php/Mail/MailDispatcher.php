<?php

declare(strict_types=1);

namespace Restatify\Shared\Mail;

final class MailDispatcher {
    /**
     * @param array<int,string> $to
     * @param array<int,string> $headers
     * @param array<int,string> $attachments
     */
    public static function send(
        array $to,
        string $subject,
        string $htmlBody,
        string $textBody,
        bool $htmlEnabled,
        array $headers = [],
        array $attachments = [],
        ?string $fromAddress = null,
        ?string $fromName = null
    ): bool {
        if (count($to) === 0) {
            return false;
        }

        $subject = trim($subject);
        $htmlBody = trim($htmlBody);
        $textBody = trim($textBody);

        if ($subject === '' || ($htmlBody === '' && $textBody === '')) {
            return false;
        }

        if ($textBody === '') {
            $textBody = wp_strip_all_tags($htmlBody);
        }

        $cleanTo = array_values(array_filter(array_map('sanitize_email', $to), 'is_email'));
        if (count($cleanTo) === 0) {
            return false;
        }

        $fromCallback = null;
        $fromNameCallback = null;
        $effectiveFromAddress = null;
        $localMailpitTransport = false;

        if (is_string($fromAddress) && $fromAddress !== '' && is_email($fromAddress)) {
            $effectiveFromAddress = $fromAddress;
        } else {
            $host = (string) wp_parse_url(home_url(), PHP_URL_HOST);
            if ($host === '' || $host === 'localhost' || !str_contains($host, '.')) {
                $localMailpitTransport = true;
                $adminEmail = (string) get_option('admin_email', '');
                if (is_email($adminEmail)) {
                    $effectiveFromAddress = $adminEmail;
                }
            }
        }

        if (is_string($effectiveFromAddress) && $effectiveFromAddress !== '') {
            $fromCallback = static function () use ($effectiveFromAddress): string {
                return $effectiveFromAddress;
            };
            add_filter('wp_mail_from', $fromCallback);
        }

        if (is_string($fromName) && $fromName !== '') {
            $fromNameCallback = static function () use ($fromName): string {
                return $fromName;
            };
            add_filter('wp_mail_from_name', $fromNameCallback);
        }

        if (!$htmlEnabled || $htmlBody === '') {
            $plainHeaders = $headers;
            $plainHeaders[] = 'Content-Type: text/plain; charset=UTF-8';

            $transportCallback = null;
            if ($localMailpitTransport) {
                $transportCallback = static function ($phpmailer): void {
                    $phpmailer->isSMTP();
                    $phpmailer->Host = '127.0.0.1';
                    $phpmailer->Port = 1025;
                    $phpmailer->SMTPAuth = false;
                    if (property_exists($phpmailer, 'SMTPAutoTLS')) {
                        $phpmailer->SMTPAutoTLS = false;
                    }
                };
                add_action('phpmailer_init', $transportCallback);
            }

            $sent = wp_mail($cleanTo, wp_strip_all_tags($subject), $textBody, $plainHeaders, $attachments);

            if ($transportCallback !== null) {
                remove_action('phpmailer_init', $transportCallback);
            }

            if ($fromCallback !== null) {
                remove_filter('wp_mail_from', $fromCallback);
            }
            if ($fromNameCallback !== null) {
                remove_filter('wp_mail_from_name', $fromNameCallback);
            }

            return (bool) $sent;
        }

        $phpMailerCallback = static function ($phpmailer) use ($htmlBody, $textBody, $localMailpitTransport): void {
            if ($localMailpitTransport) {
                $phpmailer->isSMTP();
                $phpmailer->Host = '127.0.0.1';
                $phpmailer->Port = 1025;
                $phpmailer->SMTPAuth = false;
                if (property_exists($phpmailer, 'SMTPAutoTLS')) {
                    $phpmailer->SMTPAutoTLS = false;
                }
            }
            $phpmailer->isHTML(true);
            $phpmailer->Body = $htmlBody;
            $phpmailer->AltBody = $textBody;
        };

        add_action('phpmailer_init', $phpMailerCallback);
        $sent = wp_mail($cleanTo, wp_strip_all_tags($subject), $htmlBody, $headers, $attachments);
        remove_action('phpmailer_init', $phpMailerCallback);

        if ($fromCallback !== null) {
            remove_filter('wp_mail_from', $fromCallback);
        }
        if ($fromNameCallback !== null) {
            remove_filter('wp_mail_from_name', $fromNameCallback);
        }

        return (bool) $sent;
    }
}
