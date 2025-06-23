<?php

namespace App\Domain\UserListening\ValueObjects;

use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\ValueObjects\PlaylistId;
use App\Domain\UserListening\ValueObjects\PlaylistType;

class Playlist {
    private PlaylistId $id;
    private UserId $userId;
    private string $name;
    private PlaylistType $type;
    private array $songIds = [];  // just ids

    public function __construct(UserId $userId, string $name, PlaylistType $type) {
        $this->id = PlaylistId::generate();
        $this->userId = $userId;
        $this->name = $name;
        $this->type = $type;
    }

    public function addSong(SongId $songId): void {
        // if (count($this->songIds) >= 20) {
        //     throw new \DomainException('Mix can only have 20 songs');
        // }

        if (!in_array($songId, $this->songIds)) {
            $this->songIds[] = $songId;
        }
    }

    // Getters
    public function getId(): PlaylistId {
        return $this->id;
    }

    public function getUserId(): UserId {
        return $this->userId;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): PlaylistType {
        return $this->type;
    }

    // returns array of SongId type
    public function getSongIds(): array {
        return $this->songIds;
    }

    public function getSongCount(): int {
        return count($this->songIds);
    }

    public static function fromPersistance(
        PlaylistId $id,
        UserId $userId,
        string $name,
        PlaylistType $type,
        array $songIds = []
    ) {
        $playlist = new self($userId, $name, $type);
        $playlist->id = $id;
        $playlist->songIds = $songIds;
        return $playlist;
    }
}