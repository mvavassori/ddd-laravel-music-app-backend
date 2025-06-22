<?php

namespace App\Contracts\Domain\MusicCatalog\Repositories;

use App\Domain\MusicCatalog\Entities\Song;
use App\Domain\MusicCatalog\ValueObjects\SongId;

interface SongRepositoryInterface {
    public function find(SongId $id);
    public function findWithContributions(SongId $id);
    public function create(Song $song);
    public function update(Song $song);
    public function delete(SongId $id);
    // public function getSongsByGenreAtRandom($genre, $limit = 10);
}