<?php

declare(strict_types=1);

namespace Restatify\Shared\Contracts;

final class BookingChatTokens {
    public const OPEN = '[[RESTATIFY_BOOKING_OPEN]]';
    public const CONFIRMED = '[[RESTATIFY_BOOKING_CONFIRMED]]';
    public const CANCELLED = '[[RESTATIFY_BOOKING_CANCELLED]]';

    public static function defineGlobalConstants(): void {
        if (!defined('RESTATIFY_BOOKING_OPEN_TOKEN')) {
            define('RESTATIFY_BOOKING_OPEN_TOKEN', self::OPEN);
        }

        if (!defined('RESTATIFY_BOOKING_CONFIRMED_TOKEN')) {
            define('RESTATIFY_BOOKING_CONFIRMED_TOKEN', self::CONFIRMED);
        }

        if (!defined('RESTATIFY_BOOKING_CANCELLED_TOKEN')) {
            define('RESTATIFY_BOOKING_CANCELLED_TOKEN', self::CANCELLED);
        }
    }
}
