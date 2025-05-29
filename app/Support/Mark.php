<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\HtmlString;

class Mark implements Castable
{
    public ?string $summary;
    public ?HtmlString $html;

    public function __construct(public ?string $up, public ?string $down)
    {
        $this->summary = is_string($up) ? Str::words(strip_tags($up), 20) : null;
        $this->html = is_string($up) ? new HtmlString($up) : null;
    }

    public static function fromArray(array $array)
    {
        return new static($array['up'], $array['down']);
    }

    public static function fromString(string $string)
    {
        return static::fromArray(json_decode($string, true));
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
        return new class implements CastsAttributes {
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

                if (!$value instanceof Mark) {
                    throw new \InvalidArgumentException('Expected an instanceof of App\Support\Mark');
                }

                return $value->toString();
            }
        };
    }
}
