<?php

declare(strict_types=1);

namespace Restatify\Shared\Api;

final class BookingApiErrorFormatter {
    /**
     * Extracts a user-facing message from a decoded API error payload.
     *
     * @param mixed $body
     */
    public static function extractMessage($body): string {
        if (!is_array($body)) {
            return '';
        }

        if (isset($body['message']) && is_string($body['message'])) {
            return trim($body['message']);
        }

        if (isset($body['detail']) && is_array($body['detail'])) {
            $structured = $body['detail'];
            $code = isset($structured['code']) && is_string($structured['code']) ? trim($structured['code']) : '';
            $message = isset($structured['message']) && is_string($structured['message']) ? trim($structured['message']) : '';

            if ($message !== '') {
                return $message;
            }

            if ($code !== '' && class_exists('\\Restatify\\Shared\\Contracts\\BookingApiErrorCodes', false)) {
                $mapped = \Restatify\Shared\Contracts\BookingApiErrorCodes::defaultMessageForCode($code);
                if ($mapped !== '') {
                    return $mapped;
                }
            }
        }

        return self::flattenDetail($body['detail'] ?? null);
    }

    /**
     * Converts structured API error detail values to a compact string.
     *
     * @param mixed $detail
     */
    public static function flattenDetail($detail): string {
        if (is_string($detail)) {
            return trim($detail);
        }

        if (!is_array($detail)) {
            return '';
        }

        $messages = [];
        foreach ($detail as $item) {
            if (is_string($item)) {
                $item = trim($item);
                if ($item !== '') {
                    $messages[] = $item;
                }
                continue;
            }

            if (!is_array($item)) {
                continue;
            }

            $item_message = '';
            if (isset($item['msg']) && is_string($item['msg'])) {
                $item_message = trim($item['msg']);
            } elseif (isset($item['message']) && is_string($item['message'])) {
                $item_message = trim($item['message']);
            }

            $location = '';
            if (isset($item['loc']) && is_array($item['loc'])) {
                $location_parts = array_filter(array_map('strval', $item['loc']), static fn (string $part): bool => $part !== '');
                if (count($location_parts) > 0) {
                    $location = implode(' -> ', $location_parts) . ': ';
                }
            }

            if ($item_message !== '') {
                $messages[] = $location . $item_message;
            }
        }

        return implode(' | ', array_values(array_unique($messages)));
    }
}
