<?php

namespace App\Support;

use App\Services\Content\HtmlContentCleaner;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\HtmlString;
use JsonSerializable;
use Livewire\Wireable;

class Mark implements Castable, Wireable, JsonSerializable
{
    public ?string $summary;
    public ?HtmlString $html;

    public function __construct(
        public ?string $up,
        public ?string $down,
        public ?string $raw = null,
        public string $format = 'html'
    ) {
        $this->summary = $up;
        $this->html = $down !== null ? new HtmlString($down) : null;
    }

    public static function blank(): static
    {
        return new static(null, null, null, 'html');
    }

    public static function fromArray(?array $array): static
    {
        if (! is_array($array)) {
            return static::blank();
        }

        // Preferred explicit path:
        // caller gives raw markdown and we render it server-side.
        if (array_key_exists('markdown', $array) && is_string($array['markdown'])) {
            return static::fromMarkdown($array['markdown']);
        }

        // New canonical path:
        // caller gives raw source + optional format.
        if (array_key_exists('raw', $array) && is_string($array['raw'])) {
            $format = $array['format'] ?? static::detectFormat($array['raw']);

            return $format === 'markdown'
                ? static::fromMarkdown($array['raw'])
                : static::fromHtml($array['raw']);
        }

        // Legacy up/down path.
        $up = $array['up'] ?? null;
        $down = $array['down'] ?? null;

        if ($up === null && $down === null) {
            return static::blank();
        }

        $source = $down ?? $up;

        if (! is_string($source)) {
            return static::blank();
        }

        $upText = is_string($up) ? static::toPlainText($up) : null;

        if (static::detectFormat($source) === 'markdown') {
            return static::fromMarkdown($source, $upText);
        }

        return static::fromHtml($source, $upText);
    }

    public static function fromString(?string $string): static
    {
        if ($string === null || trim($string) === '') {
            return static::blank();
        }

        $decoded = json_decode($string, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return static::fromArray($decoded);
        }

        return static::detectFormat($string) === 'markdown'
            ? static::fromMarkdown($string)
            : static::fromHtml($string);
    }

    public static function fromMarkdown(?string $markdown, ?string $text = null): static
    {
        if ($markdown === null || trim($markdown) === '') {
            return static::blank();
        }

        $html = app(MarkdownMathService::class)->render($markdown);
        $text ??= static::toPlainText($html);

        return new static($text, $html, $markdown, 'markdown');
    }

    public static function fromHtml(?string $html, ?string $text = null): static
    {
        if ($html === null || trim($html) === '') {
            return static::blank();
        }

        $clean = app(HtmlContentCleaner::class)->clean($html);
        $text ??= static::toPlainText($clean);

        return new static($text, $clean, $clean, 'html');
    }

    public function toArray(): array
    {
        return [
            'up' => $this->up,
            'down' => $this->down,
            'raw' => $this->raw,
            'format' => $this->format,
        ];
    }

    public function toString(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toLivewire()
    {
        return $this->toArray();
    }

    public static function fromLivewire($value): static
    {
        return static::fromArray($value);
    }

    public static function detectFormat(string $value): string
    {
        if (preg_match('/<(?:p|div|span|br|sup|sub|em|strong|b|i|u|table|ul|ol|li|img|math)\b/i', $value)) {
            return 'html';
        }

        if (str_contains($value, '&nbsp;') || str_contains($value, '<!--')) {
            return 'html';
        }

        return 'markdown';
    }

    public static function toPlainText(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        // Make simple math markup more useful in plain text.
        $html = preg_replace('/<sup>(.*?)<\/sup>/is', '^$1', $html) ?? $html;
        $html = preg_replace('/<sub>(.*?)<\/sub>/is', '_$1', $html) ?? $html;

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s*\n\s*/u', "\n", $text) ?? $text;
        $text = trim($text);

        return $text === '' ? null : $text;
    }

    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, string $key, $value, array $attributes)
            {
                return is_null($value) ? null : Mark::fromString($value);
            }

            public function set($model, string $key, $value, array $attributes)
            {
                if ($value instanceof Mark) {
                    return $value->toString();
                }

                if (is_array($value)) {
                    return Mark::fromArray($value)->toString();
                }

                if (is_string($value)) {
                    return Mark::fromString($value)->toString();
                }

                if (is_null($value)) {
                    return Mark::blank()->toString();
                }

                throw new \InvalidArgumentException('Expected Mark, array, string, or null.');
            }
        };
    }
}
