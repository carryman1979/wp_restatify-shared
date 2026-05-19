<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Restatify\Shared\Util\TokenReplacer;

final class TokenReplacerTest extends TestCase {
    public function testReplaceNormalizesBareTokensAndKeepsUnknownTokens(): void {
        $template = 'Hallo {name}, Kanal: {channel}, Unknown: {unknown}';

        $result = TokenReplacer::replace($template, [
            'name' => 'Anna',
            '{channel}' => 'WhatsApp',
        ]);

        self::assertSame('Hallo Anna, Kanal: WhatsApp, Unknown: {unknown}', $result);
    }

    public function testReplaceAllAppliesReplacementToEachTemplateEntry(): void {
        $result = TokenReplacer::replaceAll(
            [
                'subject' => 'Termin mit {name}',
                'body' => 'Hallo {name}, wir sehen uns am {date}.',
            ],
            [
                'name' => 'Max',
                'date' => '20.05.2026',
            ]
        );

        self::assertSame('Termin mit Max', $result['subject']);
        self::assertSame('Hallo Max, wir sehen uns am 20.05.2026.', $result['body']);
    }
}
