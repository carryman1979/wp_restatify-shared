<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Restatify\Shared\Api\BookingApiErrorFormatter;

final class BookingApiErrorFormatterTest extends TestCase {
    public function testExtractMessageReturnsTopLevelMessage(): void {
        $message = BookingApiErrorFormatter::extractMessage([
            'message' => 'Custom backend failure',
        ]);

        self::assertSame('Custom backend failure', $message);
    }

    public function testExtractMessageMapsKnownErrorCode(): void {
        $message = BookingApiErrorFormatter::extractMessage([
            'detail' => [
                'code' => 'SLOT_UNAVAILABLE',
            ],
        ]);

        self::assertSame('Slot is no longer available', $message);
    }

    public function testFlattenDetailFormatsStructuredValidationErrors(): void {
        $message = BookingApiErrorFormatter::flattenDetail([
            [
                'loc' => ['body', 'start_iso'],
                'msg' => 'must include timezone',
            ],
            [
                'loc' => ['body', 'email'],
                'message' => 'is invalid',
            ],
        ]);

        self::assertSame('body -> start_iso: must include timezone | body -> email: is invalid', $message);
    }
}
