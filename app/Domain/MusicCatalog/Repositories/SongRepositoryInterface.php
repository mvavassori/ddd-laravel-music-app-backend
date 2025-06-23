<?php

namespace App\Domain\MusicCatalog\Repositories;

use App\Domain\MusicCatalog\Entities\Song;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\MusicCatalog\ValueObjects\GenreId;

interface SongRepositoryInterface {
    public function find(SongId $id);
    public function findWithContributions(SongId $id);
    public function create(Song $song);
    public function update(Song $song);
    public function delete(SongId $id);
    public function getSongIdsByGenreAtRandom(GenreId $genreId, $limit = 10);
}