<?php

namespace App\Domain\UserListening\Repositories;

use App\Domain\UserListening\ValueObjects\Play;
use App\Domain\UserListening\ValueObjects\UserId;

interface PlayRepositoryInterface {
    public function create(Play $play);
    public function getMostPlayedSongIdsByUser(UserId $userId, $limit = 10);
    public function getTopGenreByUser(UserId $userId);
}