<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Restatify\Shared\Security\CaptchaVerifier;

final class CaptchaVerifierTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        $GLOBALS['restatify_shared_http_response'] = [
            'response' => ['code' => 200],
            'body' => '{"success":true,"score":0.9,"action":"comment"}',
        ];
    }

    public function testRejectsMissingSecretsAndTokensWithoutMakingARequest(): void {
        $GLOBALS['restatify_shared_http_calls'] = [];

        self::assertFalse(CaptchaVerifier::verify('recaptcha', '', 'token'));
        self::assertFalse(CaptchaVerifier::verify('turnstile', 'secret', ''));
        self::assertSame([], $GLOBALS['restatify_shared_http_calls']);
    }

    public function testVerifiesRecaptchaScoreAndExpectedAction(): void {
        self::assertTrue(CaptchaVerifier::verify('recaptcha', 'secret', 'token', '192.0.2.1', 0.5, 'comment'));
        self::assertFalse(CaptchaVerifier::verify('recaptcha', 'secret', 'token', '', 0.95, 'comment'));
        self::assertFalse(CaptchaVerifier::verify('recaptcha', 'secret', 'token', '', 0.5, 'other'));
    }

    public function testVerifiesTurnstileAndRejectsInvalidProviderResponses(): void {
        self::assertTrue(CaptchaVerifier::verify('turnstile', 'secret', 'token', '', 0.5, 'comment'));

        $GLOBALS['restatify_shared_http_response']['body'] = '{"success":false}';
        self::assertFalse(CaptchaVerifier::verify('turnstile', 'secret', 'token'));

        $GLOBALS['restatify_shared_http_response']['response']['code'] = 503;
        self::assertFalse(CaptchaVerifier::verify('turnstile', 'secret', 'token'));
    }

    public function testRejectsUnknownProvidersAndTransportErrors(): void {
        self::assertFalse(CaptchaVerifier::verify('unknown', 'secret', 'token'));

        $GLOBALS['restatify_shared_http_response'] = new WP_Error('network_error');
        self::assertFalse(CaptchaVerifier::verify('turnstile', 'secret', 'token'));
    }

    public function testRejectsMalformedResponsesAndMissingScores(): void {
        foreach (['not-json', 'null', '{"success":true}', '{"success":true,"score":"invalid"}'] as $body) {
            $GLOBALS['restatify_shared_http_response']['body'] = $body;
            self::assertFalse(CaptchaVerifier::verify('recaptcha', 'secret', 'token'));
        }
    }
}
