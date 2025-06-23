<?php

namespace App\Domain\UserListening\Repositories;

use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\ValueObjects\Playlist;
use App\Domain\UserListening\ValueObjects\PlaylistId;

interface PlaylistRepositoryInterface {
    public function find(PlaylistId $id);
    // public function findWithRelations(PlaylistId $id, array $relations);
    public function create(Playlist $playlist);
    // public function update(Playlist $playlist);
    // public function delete(PlaylistId $id);
    // public function findByUser(UserId $userId);
    public function findByUserAndType(UserId $userId, $type, $date = null);
    // public function attachSongs(PlaylistId $playlistId, array $songIds);
}