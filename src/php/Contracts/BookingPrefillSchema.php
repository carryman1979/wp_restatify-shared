<?php

declare(strict_types=1);

namespace Restatify\Shared\Contracts;

final class BookingPrefillSchema {
    public const TOKEN = '[[RESTATIFY_BOOKING_PREFILL]]';

    /**
     * @var array<int,string>
     */
    public const ALLOWED_KEYS = [
        'intent',
        'subject',
        'note',
        'name',
        'email',
        'contact_method',
        'contact_value',
        'date',
        'time',
    ];

    /**
     * @var array<int,string>
     */
    public const DEFAULT_CONTACT_METHODS = [
        'phone',
        'whatsapp',
        'teams',
        'zoom',
        'google_meet',
        'signal',
    ];

    /**
     * @return array<int,string>
     */
    public static function allowedKeys(): array {
        return self::ALLOWED_KEYS;
    }

    /**
     * @return array<int,string>
     */
    public static function defaultContactMethods(): array {
        return self::DEFAULT_CONTACT_METHODS;
    }
}
