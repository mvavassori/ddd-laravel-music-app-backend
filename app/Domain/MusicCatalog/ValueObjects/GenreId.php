<?php

namespace App\Domain\MusicCatalog\ValueObjects;

use Illuminate\Support\Str;

class GenreId {
    private string $value;
    public function __construct(string $value) {
        if (!Str::isUuid($value)) {
            throw new \InvalidArgumentException("Invalid Genre UUID string: $value");
        }
        $this->value = $value;
    }

    public static function generate(): GenreId {
        return new self(Str::orderedUuid());
    }

    public function getValue(): string {
        return (string) $this->value;
    }
}