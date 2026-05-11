<?php

declare(strict_types=1);

namespace Restatify\Shared\Runtime;

final class PluginState {
    public static function isPluginActive(string $pluginRelativePath): bool {
        if ($pluginRelativePath === '') {
            return false;
        }

        $activePlugins = get_option('active_plugins', []);
        if (is_array($activePlugins) && in_array($pluginRelativePath, $activePlugins, true)) {
            return true;
        }

        if (is_multisite()) {
            $networkPlugins = get_site_option('active_sitewide_plugins', []);
            if (is_array($networkPlugins) && isset($networkPlugins[$pluginRelativePath])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int,string> $legacyBasenames
     */
    public static function deactivateLegacyPlugins(array $legacyBasenames): bool {
        $changed = false;
        $activePlugins = get_option('active_plugins', []);

        if (is_array($activePlugins)) {
            $filtered = array_values(array_filter(
                $activePlugins,
                static function ($plugin) use ($legacyBasenames): bool {
                    return !in_array((string) $plugin, $legacyBasenames, true);
                }
            ));

            if (count($filtered) !== count($activePlugins)) {
                update_option('active_plugins', $filtered);
                $changed = true;
            }
        }

        if (is_multisite()) {
            $sitewidePlugins = get_site_option('active_sitewide_plugins', []);
            if (is_array($sitewidePlugins)) {
                $sitewideChanged = false;
                foreach ($legacyBasenames as $basename) {
                    if (isset($sitewidePlugins[$basename])) {
                        unset($sitewidePlugins[$basename]);
                        $sitewideChanged = true;
                    }
                }

                if ($sitewideChanged) {
                    update_site_option('active_sitewide_plugins', $sitewidePlugins);
                    $changed = true;
                }
            }
        }

        return $changed;
    }

    public static function isLightstartAvailable(): bool {
        if (!defined('WP_PLUGIN_DIR')) {
            return false;
        }

        if (!file_exists(WP_PLUGIN_DIR . '/wp-maintenance-mode/wp-maintenance-mode.php')) {
            return false;
        }

        return self::isPluginActive('wp-maintenance-mode/wp-maintenance-mode.php');
    }
}
