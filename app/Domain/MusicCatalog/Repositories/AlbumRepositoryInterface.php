<?php

namespace App\Domain\MusicCatalog\Repositories;

use App\Domain\MusicCatalog\Entities\Album;
use App\Domain\MusicCatalog\ValueObjects\AlbumId;


interface AlbumRepositoryInterface {
    public function find(AlbumId $id);
    // public function findWithRelations($id, array $relations);
    public function findWithSongs(AlbumId $id);
    public function create(Album $album);
    public function update(Album $album);
    public function delete(AlbumId $id);
}