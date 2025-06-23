<?php

namespace App\Infrastructure\Persistance\Repositories;

use App\Domain\UserListening\Entities\User;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Infrastructure\Persistance\Mappers\UserMapper;
use App\Infrastructure\Persistance\Models\EloquentUserModel;
use App\Domain\UserListening\Repositories\UserRepositoryInterface;

class EloquentUserRepository implements UserRepositoryInterface {
    private UserMapper $userMapper;
    public function __construct(UserMapper $userMapper) {
        $this->userMapper = $userMapper;
    }
    public function find(UserId $id) {
        $eloquentUser = EloquentUserModel::find($id->getValue());
        if (!$eloquentUser) {
            return null;
        }
        return $this->userMapper->toDomain($eloquentUser);
    }

    public function create(User $user) {
        $eloquentUser = EloquentUserModel::create($this->userMapper->toPersistence($user));
        return $this->userMapper->toDomain($eloquentUser);
    }
}