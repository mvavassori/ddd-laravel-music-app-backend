<?php

namespace App\Infrastructure\Persistance\Mappers;

use App\Domain\MusicCatalog\Entities\Genre;
use App\Domain\MusicCatalog\ValueObjects\GenreId;
use App\Infrastructure\Persistance\Models\EloquentGenreModel;

class GenreMapper {
    public function toDomain(EloquentGenreModel $eloquentGenre) {
        $id = new GenreId($eloquentGenre->id);

        $genre = Genre::fromPersistance($id, $eloquentGenre->name);
        return $genre;
    }

    public function toPersistence(Genre $genre) {
        return [
            'id' => $genre->getId()->getValue(),
            'name' => $genre->getName()
        ];
    }
}