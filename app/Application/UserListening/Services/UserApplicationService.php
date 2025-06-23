<?php

namespace App\Application\UserListening\Services;

use App\Domain\UserListening\Entities\User;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\ValueObjects\UserEmail;
use App\Domain\UserListening\Repositories\UserRepositoryInterface;

class UserApplicationService {
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function createUser(string $name, string $email) {
        $email = new UserEmail($email);
        $user = new User($name, $email);

        return [
            'id' => $user->getId()->getValue(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];
    }

    public function findUser(string $id) {
        $userId = new UserId($id);
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new \DomainException("Artist with ID: {$id} not found");
        }
        return [
            'id' => $user->getId()->getValue(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];
    }
}