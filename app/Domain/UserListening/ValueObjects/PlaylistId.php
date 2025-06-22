<?php

namespace App\Domain\UserListening\ValueObjects;

use Illuminate\Support\Str;

class PlaylistId {
    private string $value;

    public function __construct(string $value) {
        if (!Str::isUuid($value)) {
            throw new \DomainException("Invalid Playlist UUID string: $value");
        }
        $this->value = $value;
    }

    public static function generate(): PlaylistId {
        return new self(Str::orderedUuid());
    }

    public function getValue(): string {
        return $this->value;
    }

    public function equals(PlaylistId $other): bool {
        return $this->value === $other->value;
    }
}