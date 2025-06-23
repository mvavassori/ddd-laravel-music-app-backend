<?php

namespace App\Application\MusicCatalog\Services;

use App\Domain\MusicCatalog\Entities\Genre;
use App\Domain\MusicCatalog\ValueObjects\GenreId;
use App\Domain\MusicCatalog\Repositories\GenreRepositoryInterface;

class GenreApplicationService {
    private GenreRepositoryInterface $genreRepository;

    public function __construct(GenreRepositoryInterface $genreRepository) {
        $this->genreRepository = $genreRepository;
    }

    public function findGenre(string $id) {
        $genreId = new GenreId($id);
        $genre = $this->genreRepository->find($genreId);
        if (!$genre) {
            throw new \DomainException("Genre with ID: {$id} not found");
        }
        return $this->toArray($genre);
    }

    public function findAllGenres() {
        $genres = $this->genreRepository->index();
        $genresArray = [];
        foreach ($genres as $genre) {
            $genresArray[] = $this->toArray($genre);
        }
        return $genresArray;
    }

    public function createGenre(string $name) {
        $existingGenre = $this->genreRepository->findByName($name);
        if ($existingGenre) {
            throw new \DomainException("Genre {$name} already exists");
        }
        $genre = new Genre($name);

        $this->genreRepository->create($genre);

        return $this->toArray($genre);
    }

    private function toArray(Genre $genre) {
        return [
            'id' => $genre->getId()->getValue(),
            'name' => $genre->getName()
        ];
    }
}