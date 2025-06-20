<?php

namespace App\Infrastructure\Persistance\Mappers;

use App\Domain\MusicCatalog\Entities\Song;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Infrastructure\Persistance\Models\EloquentSongModel;



class SongMapper {
    public function toDomain(EloquentSongModel $eloquentSong): Song {

        $id = new SongId($eloquentSong->id);

        $album = Song::fromPersistance(
            $id,
            $eloquentSong->title,
            $eloquentSong->genreId,
            $eloquentSong->albumId
        );
        return $album;
    }

    public function toPersistence(Song $song) {
        return [
            'id' => $song->getId()->getValue(),
            'title' => $song->getTitle(),
            'genre_id' => $song->getGenreId(),
            'album_id' => $song->getAlbumId(),
        ];
    }
}