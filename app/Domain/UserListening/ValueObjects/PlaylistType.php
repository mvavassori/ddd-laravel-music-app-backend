<?php

namespace App\Domain\UserListening\ValueObjects;

class PlaylistType {
    private string $value;

    public function __construct(string $value) {
        if (!in_array($value, ['custom', 'daily_mix'])) {
            throw new \DomainException("Invalid Playlist Type: $value");
        }
        $this->value = $value;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function equals(PlaylistType $other): bool {
        return $this->value === $other->value;
    }
}