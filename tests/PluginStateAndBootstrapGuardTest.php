<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Restatify\Shared\Runtime\BootstrapGuard;
use Restatify\Shared\Runtime\PluginState;

final class PluginStateAndBootstrapGuardTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        $GLOBALS['restatify_shared_test_options'] = [];
        $GLOBALS['restatify_shared_test_site_options'] = [];
        $GLOBALS['restatify_shared_test_transients'] = [];
        $GLOBALS['restatify_shared_test_is_multisite'] = false;
    }

    public function testIsPluginActiveFindsSingleSiteAndNetworkActivation(): void {
        $GLOBALS['restatify_shared_test_options']['active_plugins'] = [
            'wp-restatify-foo/wp-restatify-foo.php',
        ];

        self::assertTrue(PluginState::isPluginActive('wp-restatify-foo/wp-restatify-foo.php'));

        $GLOBALS['restatify_shared_test_is_multisite'] = true;
        $GLOBALS['restatify_shared_test_options']['active_plugins'] = [];
        $GLOBALS['restatify_shared_test_site_options']['active_sitewide_plugins'] = [
            'wp-restatify-network/wp-restatify-network.php' => 123456,
        ];

        self::assertTrue(PluginState::isPluginActive('wp-restatify-network/wp-restatify-network.php'));
    }

    public function testDeactivateLegacyPluginsRemovesFromBothScopes(): void {
        $GLOBALS['restatify_shared_test_is_multisite'] = true;
        $GLOBALS['restatify_shared_test_options']['active_plugins'] = [
            'legacy-a/plugin.php',
            'current/plugin.php',
            'legacy-b/plugin.php',
        ];
        $GLOBALS['restatify_shared_test_site_options']['active_sitewide_plugins'] = [
            'legacy-a/plugin.php' => time(),
            'current/plugin.php' => time(),
        ];

        $changed = PluginState::deactivateLegacyPlugins(['legacy-a/plugin.php', 'legacy-b/plugin.php']);

        self::assertTrue($changed);
        self::assertSame(['current/plugin.php'], $GLOBALS['restatify_shared_test_options']['active_plugins']);
        self::assertArrayNotHasKey('legacy-a/plugin.php', $GLOBALS['restatify_shared_test_site_options']['active_sitewide_plugins']);
    }

    public function testBootstrapGuardSetsNoticeTransientWhenLegacyPluginsDeactivated(): void {
        $GLOBALS['restatify_shared_test_options']['active_plugins'] = [
            'legacy-a/plugin.php',
            'current/plugin.php',
        ];

        $changed = BootstrapGuard::deactivateLegacyAndMaybeNotify(
            ['legacy-a/plugin.php'],
            'restatify_notice_test',
            'Legacy plugin deaktiviert.',
            'restatify-shared'
        );

        self::assertTrue($changed);
        self::assertArrayHasKey('restatify_notice_test', $GLOBALS['restatify_shared_test_transients']);

        $notice = $GLOBALS['restatify_shared_test_transients']['restatify_notice_test']['value'] ?? [];
        self::assertSame('warning', $notice['type'] ?? null);
        self::assertSame('Legacy plugin deaktiviert.', $notice['message'] ?? null);
    }
}
