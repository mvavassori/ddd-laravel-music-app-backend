<?php

namespace App\Domain\UserListening\Entities;

use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\ValueObjects\UserEmail;

class User {
    private UserId $id;
    private string $name;
    private UserEmail $email;

    public function __construct(string $name, UserEmail $email) {
        $this->id = UserId::generate();
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(): UserId {
        return $this->id;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getEmail(): UserEmail {
        return $this->email;
    }

    public static function fromPersistence(
        UserId $id,
        string $name,
        UserEmail $email
    ) {
        $user = new self($name, $email);
        $user->id = $id;
        return $user;
    }
}