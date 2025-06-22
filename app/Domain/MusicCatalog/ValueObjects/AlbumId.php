<?php

namespace App\Domain\MusicCatalog\ValueObjects;

use Illuminate\Support\Str;

class AlbumId {
    private string $value;
    public function __construct(string $value) {
        // check if something else is generating this object that it shouldn't
        if (!Str::isUuid($value)) {
            throw new \InvalidArgumentException("Invalid Album UUID string: $value");
        }
        $this->value = $value;
    }

    // i call this without instantiating the object before and it creates the object with the unique id value for me
    public static function generate(): AlbumId {
        return new self(Str::orderedUuid());
    }

    public function getValue(): string {
        return (string) $this->value;
    }

    public function equals(AlbumId $other): bool {
        return $this->value === $other->value;
    }
}