<?php

namespace App\Infrastructure\Persistance\Mappers;

use App\Domain\UserListening\ValueObjects\Play;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\UserListening\ValueObjects\UserId;
use App\Infrastructure\Persistance\Models\EloquentPlayModel;

class PlayMapper {
    public function toDomain(EloquentPlayModel $eloquentPlay) {
        $userId = new UserId($eloquentPlay->user_id);
        $songId = new SongId($eloquentPlay->song_id);

        $play = new Play($userId, $songId);
        return $play;
    }

    public function toPersistence(Play $play) {
        return [
            'user_id' => $play->getUserId()->getValue(),
            'song_id' => $play->getSongId()->getValue()
        ];
    }
}