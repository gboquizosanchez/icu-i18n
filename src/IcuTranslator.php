<?php

declare(strict_types=1);

namespace Boquizo\I18n;

use DateTimeInterface;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use Illuminate\Support\Facades\Config;
use MessageFormatter;

final class IcuTranslator extends Translator
{
    /**
     * @param  string  $key
     * @param  array<string, mixed>  $replace
     * @param  string|null  $locale
     * @param  bool  $fallback
     * @return array<string, mixed>|string
     */
    public function get(
        $key,
        array $replace = [],
        $locale = null,
        $fallback = true,
    ): array|string {
        $locale = $locale ?: $this->locale;

        [$namespace] = $this->parseKey($key);

        $namespaces = Config::collection('icu.regionals.namespaces');

        $files = Config::collection('icu.regionals.files');

        if (! $namespaces->contains($namespace)
            || $files->contains(fn (string $file): bool => Str::startsWith($key, $file))
        ) {
            $parts = preg_split('/[-_]/', $locale);
            $locale = is_array($parts) ? $parts[0] : $locale;
        }

        $line = parent::get($key, $replace, $locale, $fallback);

        if (is_array($line)) {
            /** @var array<string, mixed> $line */
            return $line;
        }

        if (Str::contains($line, '{')) {
            $icuParams = array_map(static function (mixed $item): mixed {
                if ($item instanceof DateTimeInterface) {
                    return $item->getTimestamp();
                }

                if (is_object($item) && method_exists($item, '__toString')) {
                    return (string) $item;
                }

                return $item;
            }, $replace);

            $formatted = @MessageFormatter::formatMessage($locale, $line, $icuParams);

            if ($formatted !== false) {
                return $formatted;
            }
        }

        return $line;
    }
}
