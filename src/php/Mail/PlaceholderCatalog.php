<?php

declare(strict_types=1);

namespace Restatify\Shared\Mail;

final class PlaceholderCatalog {
    /**
     * @return array<int,string>
     */
    public static function bookingMail(): array {
        return [
            '{name}',
            '{email}',
            '{site_name}',
            '{subject}',
            '{start}',
            '{end}',
            '{timezone}',
            '{note}',
            '{reference}',
            '{contact_method}',
            '{contact_value}',
            '{contact_detail}',
            '{cancellation_url}',
            '{cancellation_reason}',
            '{cancellation_message}',
        ];
    }

    /**
     * @return array<int,string>
     */
    public static function formsMail(bool $includeFieldsTable = true): array {
        $base = [
            '{form_title}',
            '{site_name}',
            '{date}',
            '{fields_text}',
        ];

        if ($includeFieldsTable) {
            array_splice($base, 3, 0, ['{fields_table}']);
        }

        return $base;
    }
}
