<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Restatify\Shared\Runtime\RateLimiter;

final class RateLimiterTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        $_SERVER = [];
        $GLOBALS['restatify_shared_test_transients'] = [];
    }

    public function testGetClientIpPrefersForwardedHeaderFirstValidIp(): void {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'invalid, 203.0.113.5, 198.51.100.2';
        $_SERVER['REMOTE_ADDR'] = '192.0.2.1';

        self::assertSame('203.0.113.5', RateLimiter::getClientIp());
    }

    public function testHitBlocksAfterThresholdAndResetsAfterWindow(): void {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.99';
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit-test-agent';

        $prefix = 'restatify_test_rl_';
        $action = 'send_message';

        self::assertTrue(RateLimiter::hit($prefix, $action, 60, 2));
        self::assertTrue(RateLimiter::hit($prefix, $action, 60, 2));
        self::assertFalse(RateLimiter::hit($prefix, $action, 60, 2));

        $fingerprint = md5('203.0.113.99|phpunit-test-agent|send_message');
        $bucketKey = $prefix . $fingerprint;

        $GLOBALS['restatify_shared_test_transients'][$bucketKey]['value']['start'] = time() - 120;

        self::assertTrue(RateLimiter::hit($prefix, $action, 60, 2));
    }
}
