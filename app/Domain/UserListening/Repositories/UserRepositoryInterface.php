<?php

namespace App\Domain\UserListening\Repositories;

use App\Domain\UserListening\Entities\User;
use App\Domain\UserListening\ValueObjects\UserId;

interface UserRepositoryInterface {
    public function find(UserId $id);
    // public function index();
    public function create(User $user);
}