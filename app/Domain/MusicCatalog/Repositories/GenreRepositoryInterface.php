<?php

namespace App\Domain\MusicCatalog\Repositories;

use App\Domain\MusicCatalog\Entities\Genre;
use App\Domain\MusicCatalog\ValueObjects\GenreId;

interface GenreRepositoryInterface {
    public function create(Genre $role);
    public function find(GenreId $id);
    public function index();
    public function findByName($name);
    public function delete(GenreId $id);
}