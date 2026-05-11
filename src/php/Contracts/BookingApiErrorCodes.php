<?php

declare(strict_types=1);

namespace Restatify\Shared\Contracts;

final class BookingApiErrorCodes {
    public const INVALID_API_KEY = 'INVALID_API_KEY';
    public const BACKEND_UNAVAILABLE = 'BACKEND_UNAVAILABLE';
    public const START_ISO_TIMEZONE_REQUIRED = 'START_ISO_TIMEZONE_REQUIRED';
    public const SLOT_UNAVAILABLE = 'SLOT_UNAVAILABLE';
    public const SLOT_RESERVED = 'SLOT_RESERVED';
    public const GOOGLE_SLOT_CONFLICT = 'GOOGLE_SLOT_CONFLICT';
    public const CANCELLATION_TOKEN_INVALID = 'CANCELLATION_TOKEN_INVALID';
    public const CANCELLATION_NOT_ALLOWED = 'CANCELLATION_NOT_ALLOWED';

    public static function defaultMessageForCode(string $code): string {
        return match ($code) {
            self::INVALID_API_KEY => 'Invalid API key',
            self::BACKEND_UNAVAILABLE => 'Booking backend is currently unavailable. Please try again later.',
            self::START_ISO_TIMEZONE_REQUIRED => 'start_iso must include timezone',
            self::SLOT_UNAVAILABLE => 'Slot is no longer available',
            self::SLOT_RESERVED => 'Slot is already reserved',
            self::GOOGLE_SLOT_CONFLICT => 'Slot conflicts with current Google calendar data',
            self::CANCELLATION_TOKEN_INVALID => 'Cancellation token is invalid',
            self::CANCELLATION_NOT_ALLOWED => 'Reservation can no longer be cancelled',
            default => '',
        };
    }
}
