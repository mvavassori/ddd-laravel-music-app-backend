<?php

namespace App\Domain\MusicCatalog\Repositories;

use App\Domain\MusicCatalog\Entities\Artist;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;

interface ArtistRepositoryInterface {
    public function all();
    public function find(ArtistId $id);
    public function findByName($name);
    public function create(Artist $artist);
    public function update(Artist $artist);
    public function delete(ArtistId $id);
    public function findWithContributions(ArtistId $id);
    public function findWithSongs(ArtistId $id);
    public function findWithAlbums(ArtistId $id);
}