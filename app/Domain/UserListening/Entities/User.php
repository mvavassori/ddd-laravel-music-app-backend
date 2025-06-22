<?php

namespace App\Domain\UserListening\ValueObjects;

class User {
    private UserId $id;
    private string $name;
    private UserEmail $email;

    public function __construct(UserId $id, string $name, UserEmail $email) {
        $this->id = $id;
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
}