<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Restatify\Shared\Util\BookingContactMethodsResolver;

final class BookingContactMethodsResolverTest extends TestCase {
    public function testMethodsFromRawNormalizesAndDeduplicates(): void {
        $raw = "Phone|Telefon\nwhatsapp|WhatsApp\nPHONE|Telefon";

        $methods = BookingContactMethodsResolver::methodsFromRaw($raw);

        self::assertSame(['phone', 'whatsapp'], $methods);
    }

    public function testMethodsFromOptionsPrefersStructuredChannels(): void {
        $options = [
            'contact_channels' => [
                ['key' => 'Teams'],
                ['key' => 'Zoom'],
                ['key' => 'teams'],
            ],
            'contact_channels_raw' => "phone|Telefon",
        ];

        $methods = BookingContactMethodsResolver::methodsFromOptions($options);

        self::assertSame(['teams', 'zoom'], $methods);
    }

    public function testMethodsFromOptionsFallsBackToRawAndThenDefaults(): void {
        $fromRaw = BookingContactMethodsResolver::methodsFromOptions([
            'contact_channels' => [],
            'contact_channels_raw' => "signal|Signal\nphone|Telefon",
        ]);
        self::assertSame(['signal', 'phone'], $fromRaw);

        $fromDefaults = BookingContactMethodsResolver::methodsFromOptions([
            'contact_channels' => [],
            'contact_channels_raw' => '',
        ]);

        self::assertNotEmpty($fromDefaults);
        self::assertSame('phone', $fromDefaults[0]);
    }
}
