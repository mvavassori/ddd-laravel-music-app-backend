<?php

namespace App\Application\MusicCatalog\Services;

use App\Domain\MusicCatalog\Entities\Album;
use App\Domain\MusicCatalog\Repositories\ArtistRepositoryInterface;
use App\Domain\MusicCatalog\Repositories\RoleRepositoryInterface;
use App\Domain\MusicCatalog\ValueObjects\RoleId;
use App\Domain\MusicCatalog\ValueObjects\AlbumId;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Domain\MusicCatalog\ValueObjects\AlbumImageUrl;
use App\Domain\MusicCatalog\Repositories\AlbumRepositoryInterface;

class AlbumApplicationService {
    private AlbumRepositoryInterface $albumRepository;
    private ArtistRepositoryInterface $artistRepository;
    private RoleRepositoryInterface $roleRepository;

    public function __construct(AlbumRepositoryInterface $albumRepository, ArtistRepositoryInterface $artistRepository, RoleRepositoryInterface $roleRepository) {
        $this->albumRepository = $albumRepository;
        $this->artistRepository = $artistRepository;
        $this->roleRepository = $roleRepository;
    }

    public function createAlbum(string $title, ?string $description = null, ?string $imageUrlString = null, array $contributions = []) {
        foreach ($contributions as $contributor) {
            // validate that role and artists exist
            $artist = $this->artistRepository->find(new ArtistId($contributor['artist_id']));
            if (!$artist) {
                throw new \DomainException('Artist not found');
            }
            $role = $this->roleRepository->find(new RoleId($contributor['role_id']));
            if (!$role) {
                throw new \DomainException('Role not found');
            }
        }
        $albumImageUrl = null;
        if ($imageUrlString) {
            $albumImageUrl = new AlbumImageUrl($imageUrlString);
        }

        // create Album entity
        $album = new Album($title, $description, $albumImageUrl);

        foreach ($contributions as $contributor) {
            $album->addContributor(new ArtistId($contributor['artist_id']), new RoleId($contributor['role_id']));
        }

        $this->albumRepository->create($album);

        return $this->toArray($album, true);
    }

    public function updateAlbum(
        string $id,
        ?string $title = null,
        ?string $description = null,
        ?string $imageUrlString = null,
        ?array $contributions = null
    ) {
        $album = $this->albumRepository->find(new AlbumId($id));
        if (!$album) {
            throw new \DomainException('Album not found');
        }

        if ($title !== null) {
            $album->updateTitle($title);
        }

        if ($description !== null) {
            $album->updateDescription($description);
        }

        if ($imageUrlString !== null) {
            $imageUrl = null;
            if ($imageUrlString !== '') {
                $imageUrl = new AlbumImageUrl($imageUrlString);
            }
            $album->updateImageUrl($imageUrl);
        }

        // handle contributors replacement
        if ($contributions !== null) {
            // validate all contributors first
            $validatedContributions = [];
            foreach ($contributions as $contributor) {
                $artist = $this->artistRepository->find(new ArtistId($contributor['artist_id']));
                if (!$artist) {
                    throw new \DomainException("Artist {$contributor['artist_id']} not found");
                }

                $role = $this->roleRepository->find(new RoleId($contributor['role_id']));
                if (!$role) {
                    throw new \DomainException("Role {$contributor['role_id']} not found");
                }

                $validatedContributions[] = [
                    'artistId' => new ArtistId($contributor['artist_id']),
                    'roleId' => new RoleId($contributor['role_id'])
                ];
            }

            // Replace all contributors
            $album->replaceContributors($validatedContributions);
        }

        return $this->toArray($album, true);
    }

    public function findAlbum(string $id) {
        $albumId = new AlbumId($id);
        $album = $this->albumRepository->find($albumId);
        if (!$album) {
            throw new \DomainException("Album with ID: {$id} not found");
        }
        return $this->toArray($album);
    }

    public function deleteAlbum(string $id) {
        $album = $this->albumRepository->find(new AlbumId($id));

        if (!$album) {
            throw new \DomainException("Album not found");
        }
        $this->albumRepository->delete(new AlbumId($id));
    }

    private function toArray(Album $album, bool $withContributions = false): array {
        if ($withContributions) {
            return [
                'id' => $album->getId()->getValue(),
                'title' => $album->getTitle(),
                'description' => $album->getDescription(),
                'image_url' => $album->getImageUrl()?->getValue(),
                'contributions' => array_map(function ($contribution) {
                    return [
                        'artist_id' => $contribution->getArtistId()->getValue(),
                        'role_id' => $contribution->getRoleId()->getValue()
                    ];
                }, $album->getContributions())
            ];

        }
        return [
            'id' => $album->getId()->getValue(),
            'name' => $album->getTitle(),
            'description' => $album->getDescription(),
            'image_url' => $album->getImageUrl()?->getValue(),
        ];
    }
}