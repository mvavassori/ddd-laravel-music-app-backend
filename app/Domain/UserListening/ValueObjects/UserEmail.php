<?php

namespace App\Domain\UserListening\ValueObjects;

class UserEmail {
    private string $value;

    public function __construct(string $value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException("Invalid email address: $value");
        }
        $this->value = $value;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function equals(UserEmail $other): bool {
        return $this->value === $other->value;
    }
}