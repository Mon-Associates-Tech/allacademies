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
    
    // Reject non-array, non-null values
    if (!is_array($decoded)) {
        return new static(null, null);
    }
    
    return static::fromArray($decoded);
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
                if (is_string($value)) {
                    $value = Mark::fromString($value);
                }

                if (is_array($value)) {
                    $value = Mark::fromArray($value);
                }

                if (! $value instanceof Mark) {
                    if (is_null($value)) {
                        return (new static(null, null))->toString();
                    }
                    
                    throw new \InvalidArgumentException('Expected an instanceof of App\Support\Mark');
                }

                return $value->toString();
            }
        };
    }
}
