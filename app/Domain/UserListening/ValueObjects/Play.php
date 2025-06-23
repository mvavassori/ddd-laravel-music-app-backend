<?php
namespace App\Domain\UserListening\ValueObjects;


use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\UserListening\ValueObjects\UserId;

class Play {
    private UserId $userId;
    private SongId $songId;

    public function __construct(UserId $userId, SongId $songId) {
        $this->userId = $userId;
        $this->songId = $songId;
    }

    public function getUserId(): UserId {
        return $this->userId;
    }

    public function getSongId(): SongId {
        return $this->songId;
    }
}