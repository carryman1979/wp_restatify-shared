<?php

declare(strict_types=1);

namespace Restatify\Shared\Util;

final class BookingContactChannels {
    /**
     * @return array<string,array{label:string,input_kind:string,placeholder:string,value_label:string,ics_template:string}>
     */
    public static function defaultProfiles(): array {
        return BookingContactChannelProfiles::defaultProfiles();
    }

    /**
     * @param callable(string):string|null $translate
     * @return array<int,array{key:string,label:string,input_kind:string,placeholder:string,value_label:string,ics_template:string}>
     */
    public static function defaultRows(?callable $translate = null): array {
        return BookingContactChannelProfiles::defaultRows($translate);
    }

    /**
     * @param callable(string):string|null $translate
     */
    public static function defaultRaw(?callable $translate = null): string {
        return BookingContactChannelProfiles::defaultRaw($translate);
    }

    /**
     * @param array<string,mixed> $options
     * @return array<int,string>
     */
    public static function methodsFromOptions(array $options): array {
        return BookingContactMethodsResolver::methodsFromOptions($options);
    }

    /**
     * @return array<int,string>
     */
    public static function methodsFromRaw(string $raw): array {
        return BookingContactMethodsResolver::methodsFromRaw($raw);
    }

    /**
     * @return array<int,string>
     */
    public static function defaultMethods(): array {
        return BookingContactMethodsResolver::defaultMethods();
    }
}
