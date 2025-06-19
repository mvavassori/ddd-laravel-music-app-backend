<?php

namespace App\Application\MusicCatalog\Services;

use App\Domain\MusicCatalog\Repositories\ArtistRepositoryInterface;

class ArtistApplicationService {
    private ArtistRepositoryInterface $artistRepository;

    public function __construct(ArtistRepositoryInterface $artistRepository) {
        $this->artistRepository = $artistRepository; // todo instruct laravel container on which concretion to use
    }

    public function createArtist() {

    }
}