<?php

namespace App\Support;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Mark implements Castable
{
    public ?string $summary;
    public ?HtmlString $html;

    public function __construct(public ?string $up, public ?string $down)
    {
        $this->summary = is_string($down) ? Str::words(strip_tags($down), 20) : null;
        $this->html = is_string($down) ? new HtmlString($down) : null;
    }

    public static function fromArray(?array $array)
    {
        if (!is_array($array)) {
            return new static(null, null);
        }
        
        return new static($array['up'] ?? null, $array['down'] ?? null);
    }

    public static function fromString(?string $string)
    {
        if (is_null($string) || $string === '') {
            return new static(null, null);
        }
        
        $decoded = json_decode($string, true);
        
        // If it decodes to a valid array, use it
        if (is_array($decoded)) {
            return static::fromArray($decoded);
        }
        
        // Otherwise, treat the plain string as the 'down' (HTML) value
        // and automatically generate the 'up' (plain text summary) value
        return new static(Str::words(strip_tags($string), 20), $string);
    }

    public function toArray(): array
    {
        return [
            'up' => $this->up,
            'down' => $this->down,
        ];
    }

    public function toString(): string
    {
        return json_encode($this->toArray());
    }

    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, string $key, $value, array $attributes)
            {
                return is_null($value) ? $value : Mark::fromString($value);
            }

            public function set($model, string $key, $value, array $attributes)
            {
                // 1. Already a Mark instance (e.g., preserving existing model value)
                if ($value instanceof Mark) {
                    return $value->toString();
                }

                // 2. Array submitted (e.g., from a rich text component)
                if (is_array($value)) {
                    return Mark::fromArray($value)->toString();
                }

                // 3. String submitted (e.g., from a plain textarea or rich text)
                if (is_string($value)) {
                    if ($value === '') {
                        // FIX: Use 'Mark' explicitly, not 'static', to avoid anonymous class resolution
                        return (new Mark(null, null))->toString();
                    }
                    
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        return Mark::fromArray($decoded)->toString();
                    }
                    
                    // Treat plain string as the 'down' (HTML) value
                    // FIX: Use 'Mark' explicitly
                    return (new Mark(Str::words(strip_tags($value), 20), $value))->toString();
                }

                // 4. Null submitted
                if (is_null($value)) {
                    // FIX: Use 'Mark' explicitly
                    return (new Mark(null, null))->toString();
                }

                throw new \InvalidArgumentException('Expected an instance of App\Support\Mark, array, string, or null.');
            }
        };
    }
}