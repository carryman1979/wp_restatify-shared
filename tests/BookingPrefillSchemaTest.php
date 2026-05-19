<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Restatify\Shared\Contracts\BookingPrefillSchema;

final class BookingPrefillSchemaTest extends TestCase {
    public function testTokenAndAllowedKeysAreStable(): void {
        self::assertSame('[[RESTATIFY_BOOKING_PREFILL]]', BookingPrefillSchema::TOKEN);

        $keys = BookingPrefillSchema::allowedKeys();
        self::assertContains('intent', $keys);
        self::assertContains('contact_method', $keys);
        self::assertContains('contact_value', $keys);
        self::assertContains('date', $keys);
        self::assertContains('time', $keys);
    }

    public function testDefaultContactMethodsContainCommonChannels(): void {
        $methods = BookingPrefillSchema::defaultContactMethods();

        self::assertContains('phone', $methods);
        self::assertContains('whatsapp', $methods);
        self::assertContains('teams', $methods);
        self::assertContains('zoom', $methods);
    }
}
