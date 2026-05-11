<?php

declare(strict_types=1);

namespace Restatify\Shared\Runtime;

final class BootstrapGuard {
    /**
     * @param array<int,string> $legacyBasenames
     */
    public static function deactivateLegacyAndMaybeNotify(
        array $legacyBasenames,
        string $noticeTransientKey,
        string $message,
        string $textDomain
    ): bool {
        $changed = PluginState::deactivateLegacyPlugins($legacyBasenames);

        if ($changed) {
            set_transient($noticeTransientKey, [
                'type' => 'warning',
                'message' => __($message, $textDomain),
            ], 300);
        }

        return $changed;
    }
}
