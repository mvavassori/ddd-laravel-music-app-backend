<?php

namespace App\Infrastructure\Persistance\Repositories;

use App\Domain\MusicCatalog\Entities\Genre;
use App\Domain\MusicCatalog\ValueObjects\GenreId;
use App\Infrastructure\Persistance\Mappers\GenreMapper;
use App\Infrastructure\Persistance\Models\EloquentGenreModel;
use App\Domain\MusicCatalog\Repositories\GenreRepositoryInterface;

class EloquentGenreRepository implements GenreRepositoryInterface {
    private GenreMapper $genreMapper;
    public function __construct(GenreMapper $genreMapper) {
        $this->genreMapper = $genreMapper;
    }
    public function create(Genre $genre) {
        $eloquentGenre = EloquentGenreModel::create($this->genreMapper->toPersistence($genre));
        return $this->genreMapper->toDomain($eloquentGenre);
    }

    public function index() {
        $eloquentGenres = EloquentGenreModel::all();
        $genresArrayOfObj = [];
        foreach ($eloquentGenres as $eloquentGenre) {
            $genresArrayOfObj[] = $this->genreMapper->toDomain($eloquentGenre);
        }
        return $genresArrayOfObj;
    }

    public function find(GenreId $id) {
        $eloquentGenre = EloquentGenreModel::find($id->getValue());
        if (!$eloquentGenre) {
            return null;
        }
        return $this->genreMapper->toDomain($eloquentGenre);
    }

    public function findByName($name) {
        $eloquentGenre = EloquentGenreModel::where('name', $name)->first();
        if (!$eloquentGenre) {
            return null;
        }
        return $this->genreMapper->toDomain($eloquentGenre);
    }

    public function delete(GenreId $id) {
        return EloquentGenreModel::destroy($id->getValue());
    }
}