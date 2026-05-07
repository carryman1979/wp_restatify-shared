<?php

declare(strict_types=1);

namespace Restatify\Shared\Migration;

/**
 * Reusable post-migration admin notice flow.
 *
 * Intended usage in target plugin after successful settings migration:
 * - show admin notice with two actions:
 *   - keep legacy plugins (default)
 *   - deactivate and remove legacy plugins
 */
final class MigrationNoticeManager {
    /**
     * @param array<string,mixed> $config
     */
    public static function register(array $config): void {
        add_action('admin_notices', static function () use ($config): void {
            self::render_notice($config);
        });

        add_action('admin_init', static function () use ($config): void {
            self::handle_action($config);
        });
    }

    /**
     * @param array<string,mixed> $config
     */
    private static function render_notice(array $config): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $state_key = (string) ($config['state_option_key'] ?? 'restatify_migration_notice_state');
        $state = get_option($state_key, []);
        if (!is_array($state) || empty($state['show'])) {
            return;
        }

        $page_slug = (string) ($config['page_slug'] ?? '');
        if ($page_slug !== '') {
            $current_page = sanitize_key((string) ($_GET['page'] ?? ''));
            if ($current_page !== $page_slug) {
                return;
            }
        }

        $legacy_plugins = is_array($config['legacy_plugins'] ?? null) ? $config['legacy_plugins'] : [];
        if (count($legacy_plugins) === 0) {
            return;
        }

        $lang = self::current_lang();
        $nonce = wp_create_nonce('restatify_migration_notice_action');
        $action_url = admin_url('admin.php?page=' . rawurlencode($page_slug));

        $title = $lang === 'de'
            ? 'Migration abgeschlossen'
            : 'Migration completed';

        $body = $lang === 'de'
            ? 'Die Einstellungen wurden erfolgreich migriert. Du kannst die alten Plugins behalten (empfohlen) oder deaktivieren und entfernen.'
            : 'Settings were migrated successfully. You can keep legacy plugins (recommended) or deactivate and remove them.';

        $warning = $lang === 'de'
            ? 'Hinweis: Logs und Historie werden nicht migriert und können beim Entfernen verfallen.'
            : 'Note: logs and history are not migrated and may be lost if legacy plugins are removed.';

        $keep_label = $lang === 'de' ? 'Alte Plugins behalten' : 'Keep legacy plugins';
        $remove_label = $lang === 'de' ? 'Alte Plugins deaktivieren und entfernen' : 'Deactivate and remove legacy plugins';

        $keep_url = add_query_arg([
            'restatify_migration_notice_action' => 'keep',
            'restatify_migration_notice_nonce' => $nonce,
        ], $action_url);

        $remove_url = add_query_arg([
            'restatify_migration_notice_action' => 'remove',
            'restatify_migration_notice_nonce' => $nonce,
        ], $action_url);

        echo '<div class="notice notice-success">';
        echo '<p><strong>' . esc_html($title) . '</strong></p>';
        echo '<p>' . esc_html($body) . '</p>';
        echo '<p><em>' . esc_html($warning) . '</em></p>';
        echo '<p>';
        echo '<a class="button button-primary" href="' . esc_url($keep_url) . '">' . esc_html($keep_label) . '</a> ';
        echo '<a class="button" href="' . esc_url($remove_url) . '" onclick="return confirm(' . wp_json_encode($warning) . ');">' . esc_html($remove_label) . '</a>';
        echo '</p>';
        echo '</div>';
    }

    /**
     * @param array<string,mixed> $config
     */
    private static function handle_action(array $config): void {
        if (!current_user_can('manage_options')) {
            return;
        }

        $action = sanitize_key((string) ($_GET['restatify_migration_notice_action'] ?? ''));
        if ($action === '') {
            return;
        }

        $nonce = sanitize_text_field((string) ($_GET['restatify_migration_notice_nonce'] ?? ''));
        if (!wp_verify_nonce($nonce, 'restatify_migration_notice_action')) {
            return;
        }

        $state_key = (string) ($config['state_option_key'] ?? 'restatify_migration_notice_state');
        $state = get_option($state_key, []);
        if (!is_array($state)) {
            $state = [];
        }

        if ($action === 'keep') {
            $state['show'] = false;
            $state['decision'] = 'keep';
            update_option($state_key, $state, false);
            return;
        }

        if ($action !== 'remove') {
            return;
        }

        $legacy_plugins = is_array($config['legacy_plugins'] ?? null) ? $config['legacy_plugins'] : [];
        if (count($legacy_plugins) === 0) {
            return;
        }

        if (!function_exists('deactivate_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if (!function_exists('delete_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        deactivate_plugins($legacy_plugins, true, is_multisite());

        $result = delete_plugins($legacy_plugins);
        $state['show'] = false;
        $state['decision'] = 'remove';
        $state['removed_at'] = time();
        $state['remove_result'] = is_wp_error($result) ? $result->get_error_message() : 'ok';
        update_option($state_key, $state, false);
    }

    private static function current_lang(): string {
        if (function_exists('determine_locale')) {
            $locale = (string) determine_locale();
            if (str_starts_with(strtolower($locale), 'de')) {
                return 'de';
            }
        }

        return 'en';
    }
}
