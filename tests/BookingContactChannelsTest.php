<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Restatify\Shared\Util\BookingContactChannels;

final class BookingContactChannelsTest extends TestCase {
    public function testDefaultRowsRespectsTranslatorAndFieldShapes(): void {
        $translate = static function (string $value): string {
            return 'TR_' . $value;
        };

        $rows = BookingContactChannels::defaultRows($translate);

        self::assertNotEmpty($rows);
        self::assertSame('phone', $rows[0]['key'] ?? null);
        self::assertStringStartsWith('TR_', (string) ($rows[0]['label'] ?? ''));
        self::assertStringStartsWith('TR_', (string) ($rows[0]['value_label'] ?? ''));
        self::assertStringStartsWith('TR_', (string) ($rows[0]['ics_template'] ?? ''));
    }

    public function testDefaultRawHasPipeSeparatedRows(): void {
        $raw = BookingContactChannels::defaultRaw();
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];

        self::assertNotEmpty($lines);
        self::assertStringContainsString('|', (string) $lines[0]);
    }

    public function testMethodsFromOptionsUsesResolverAlias(): void {
        $methods = BookingContactChannels::methodsFromOptions([
            'contact_channels' => [
                ['key' => 'Google Meet'],
                ['key' => 'Signal'],
            ],
        ]);

        self::assertSame(['googlemeet', 'signal'], $methods);
    }
}
