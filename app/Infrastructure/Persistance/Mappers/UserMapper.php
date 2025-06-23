<?php

namespace App\Infrastructure\Persistance\Mappers;

use App\Domain\UserListening\Entities\User;
use App\Domain\UserListening\ValueObjects\UserEmail;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Infrastructure\Persistance\Models\EloquentUserModel;

class UserMapper {
    public function toDomain(EloquentUserModel $eloquentUser): User {
        $id = new UserId($eloquentUser->id);

        $email = new UserEmail($eloquentUser->email);

        $user = User::fromPersistence(
            $id,
            $eloquentUser->name,
            $email
        );
        return $user;
    }

    public function toPersistence(User $user) {
        return [
            'id' => $user->getId()->getValue(),
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue()
        ];
    }
}