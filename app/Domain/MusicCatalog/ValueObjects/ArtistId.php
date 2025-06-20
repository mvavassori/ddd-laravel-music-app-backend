<?php

namespace App\Domain\MusicCatalog\ValueObjects;

use Illuminate\Support\Str;

class ArtistId {
    private string $value;
    public function __construct(string $value) {
        // check if id is uuid
        if (!Str::isUuid($value)) {
            throw new \InvalidArgumentException("Invalid Artist UUID string: $value");
        }
        $this->value = $value;
    }

    // i call this without instantiating the object before and it creates the object with the unique id value for me
    public static function generate(): ArtistId {
        return new self(Str::orderedUuid());
    }

    public function getValue(): string {
        return $this->value;
    }
}