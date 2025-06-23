<?php

namespace App\Infrastructure\Persistance\Mappers;

use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\ValueObjects\Playlist;
use App\Domain\UserListening\ValueObjects\PlaylistId;
use App\Domain\UserListening\ValueObjects\PlaylistType;
use App\Infrastructure\Persistance\Models\EloquentPlaylistModel;

class PlaylistMapper {
    public function toDomain(EloquentPlaylistModel $eloquentPlaylistModel) {

        $songIds = array_map(fn($songId) => 
            $songId = new SongId($songId)
         ,$eloquentModel->song_ids ?? []);
        
        $id = new PlaylistId($eloquentPlaylistModel->id);
        $userId = new UserId($eloquentPlaylistModel->user_id);
        $type = new PlaylistType($eloquentPlaylistModel->type);
        
        $playlist = Playlist::fromPersistance(id: $id, userId: $userId, name: $eloquentPlaylistModel->name, type: $type, songIds: $songIds); // should i give the song_ids array in the contstructor?

        return $playlist;
    }

    public function toPersistence(Playlist $playlist) {
        return [
            'id' => $playlist->getId()->getValue(),
            'user_id' => $playlist->getUserId()->getValue(),
            'name' => $playlist->getName(),
            'type' => $playlist->getType()->getValue(),
            'song_ids' => array_map(
                fn(SongId $songId) => $songId->getValue(),
                $playlist->getSongIds()
            ),
        ];
    }
}