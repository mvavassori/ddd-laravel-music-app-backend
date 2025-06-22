<?php

namespace App\Application\MusicCatalog\Services;

use App\Domain\MusicCatalog\Entities\Artist;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Domain\MusicCatalog\ValueObjects\ArtistImageUrl;
use App\Domain\MusicCatalog\Repositories\ArtistRepositoryInterface;

// the application layer funge da glue per tutto. These methods will be called by the controller

class ArtistApplicationService {
    private ArtistRepositoryInterface $artistRepository;

    public function __construct(ArtistRepositoryInterface $artistRepository) {
        $this->artistRepository = $artistRepository;
    }

    public function createArtist(string $name, ?string $bio = null, ?string $imageUrlString = null) {
        // validate that an artist with the specified nane already exists
        if ($this->artistRepository->findByName($name)) {
            throw new \DomainException("Artist with name: {$name} already exists");
        }

        // value obj
        $artistImageUrl = null;
        if ($imageUrlString) {
            $artistImageUrl = new ArtistImageUrl($imageUrlString);
        }

        // entity
        $artist = new Artist($name, $bio, $artistImageUrl);

        // create artist in persistance // returns Artist type
        $this->artistRepository->create($artist);

        // response that the controller gets
        return $this->toArray($artist);
    }

    public function updateArtist(
        string $id,
        ?string $name = null,
        ?string $bio = null,
        ?string $imageUrlString = null,
    ): array {
        $artist = $this->artistRepository->find(new ArtistId($id));

        if (!$artist) {
            throw new \DomainException("Artist not found");
        }

        if ($name !== null) {
            // check uniqueness if name is changing
            if ($artist->getName() !== $name) {
                $existingArtist = $this->artistRepository->findByName($name);
                if ($existingArtist && !$existingArtist->getId()->equals($artist->getId())) {
                    throw new \DomainException("Artist with name: {$name} already exists");
                }
            }
            $artist->updateName($name);
        }

        if ($bio !== null) {
            $artist->updateBio($bio);
        }

        if ($imageUrlString !== null) {
            $imageUrl = null;
            if ($imageUrlString !== '') {
                $imageUrl = new ArtistImageUrl($imageUrlString);
            }
            $artist->updateImageUrl($imageUrl);
        }

        // Save the updated entity
        $this->artistRepository->update($artist);

        return $this->toArray($artist);
    }

    public function findArtist(string $id) {
        $artistId = new ArtistId($id);
        $artist = $this->artistRepository->find($artistId);
        if (!$artist) {
            throw new \DomainException("Artist with ID: {$id} not found");
        }
        return $this->toArray($artist);
    }

    public function findAllArtists() {
        $artists = $this->artistRepository->all();
        $artistsArray = [];
        foreach ($artists as $artist) {
            array_push($artistsArray, $this->toArray($artist));
        }
        return $artistsArray;
    }

    public function findArtistWithContributions(string $id) {
        $result = $this->artistRepository->findWithContributions(new ArtistId($id));
        if (!$result) {
            throw new \DomainException("Artist with ID: {$id} not found");
        }
        return [
            'artist' => $this->toArray($result['artist']),
            'contributions' => [
                'albums' => array_map(function ($item) {
                    return [
                        'album_id' => $item['album']->getId()->getValue(),
                        'album_title' => $item['album']->getTitle(),
                        'role_id' => $item['role']->getId()->getValue(),
                        'role_name' => $item['role']->getName()
                    ];
                }, $result['albumContributions']),
                'songs' => array_map(function ($item) {
                    return [
                        'song_id' => $item['song']->getId()->getValue(),
                        'song_title' => $item['song']->getTitle(),
                        'role_id' => $item['role']->getId()->getValue(),
                        'role_name' => $item['role']->getName()
                    ];
                }, $result['songContributions'])
            ]
        ];
    }

    public function findArtistWithSongs(string $id) {
        $result = $this->artistRepository->findWithSongs(new ArtistId($id));

        if (!$result) {
            throw new \DomainException("Artist with ID: {$id} not found");
        }

        return [
            'artist' => $this->toArray($result['artist']),
            'songs' => array_map(function ($item) {
                return [
                    'id' => $item['song']->getId()->getValue(),
                    'title' => $item['song']->getTitle(),
                    'genre_id' => $item['song']->getGenreId()->getValue(),
                    'album_id' => $item['song']->getAlbumId()?->getValue(),
                    'role' => $item['role']->getName()
                ];
            }, $result['songContributions'])
        ];
    }

    public function deleteArtist(string $id) {
        $artist = $this->artistRepository->find(new ArtistId($id));

        if (!$artist) {
            throw new \DomainException("Artist not found");
        }
        $this->artistRepository->delete(new ArtistId($id));
    }

    // helper to convert artist object to array
    private function toArray(Artist $artist): array {
        return [
            'id' => $artist->getId()->getValue(),
            'name' => $artist->getName(),
            'bio' => $artist->getBio(),
            'image_url' => $artist->getImageUrl()?->getValue(),
        ];
    }
}