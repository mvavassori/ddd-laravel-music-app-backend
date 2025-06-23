<?php

namespace App\Infrastructure\Persistance\Repositories;

use App\Domain\UserListening\ValueObjects\UserId;
use App\Domain\UserListening\ValueObjects\Playlist;
use App\Domain\UserListening\ValueObjects\PlaylistId;
use App\Infrastructure\Persistance\Mappers\PlaylistMapper;
use App\Infrastructure\Persistance\Models\EloquentPlaylistModel;
use App\Domain\UserListening\Repositories\PlaylistRepositoryInterface;

class EloquentPlaylistRepository implements PlaylistRepositoryInterface {
    private PlaylistMapper $playlistMapper;
    public function __construct(PlaylistMapper $playlistMapper) {
        $this->PlaylistMapper = $playlistMapper;
    }
    public function find(PlaylistId $id) {
        $eloquentPlaylist = EloquentPlaylistModel::find($id->getValue());
        if (!$eloquentPlaylist) {
            return null;
        }
        return $this->playlistMapper->toDomain($eloquentPlaylist);
    }

    public function create(Playlist $playlist) {
        $eloquentPlaylist = EloquentPlaylistModel::create($this->playlistMapper->toPersistence($playlist));
        return $this->playlistMapper->toDomain($eloquentPlaylist);
    }

    public function findByUserAndType(UserId $userId, $type, $date = null): Playlist {
        $query = EloquentPlaylistModel::with('songs')
            ->where('user_id', $userId)
            ->where('type', $type);

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $eloquentPlaylist = $query->first();

        return $this->playlistMapper->toDomain($eloquentPlaylist);
    }
}