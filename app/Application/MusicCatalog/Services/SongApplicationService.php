<?php

namespace App\Infrastructure\Persistance\Repositories;

use App\Domain\MusicCatalog\Entities\Song;
use App\Domain\MusicCatalog\ValueObjects\RoleId;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\MusicCatalog\ValueObjects\AlbumId;
use App\Domain\MusicCatalog\ValueObjects\GenreId;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Domain\MusicCatalog\Repositories\RoleRepositoryInterface;
use App\Domain\MusicCatalog\Repositories\ArtistRepositoryInterface;
use App\Domain\MusicCatalog\Repositories\SongRepositoryInterface;

class SongApplicationService {
    private SongRepositoryInterface $songRepository;
    private ArtistRepositoryInterface $artistRepository;
    private RoleRepositoryInterface $roleRepository;

    public function __construct(ArtistRepositoryInterface $artistRepository, RoleRepositoryInterface $roleRepository, SongRepositoryInterface $songRepository) {
        $this->artistRepository = $artistRepository;
        $this->roleRepository = $roleRepository;
        $this->songRepository = $songRepository;
    }

    public function findSong(int $id) {
        $song = $this->songRepository->find(new SongId($id));
        if (!$song) {
            throw new \DomainException('Song not found');
        }
        return $this->toArray($song);
    }

    public function findSongWithContributions($id) {
        $song = $this->songRepository->findWithContributions(new SongId($id));
        if (!$song) {
            throw new \DomainException('Song not found');
        }

        // Convert contributions to array format
        $contributions = [];
        foreach ($song['contributions'] as $contribution) {
            $contributions[] = [
                'artist_id' => $contribution['artist']->getId()->getValue(),
                'role_id' => $contribution['role']->getId()->getValue(),
            ];
        }

        return [
            'id' => $song['song']->getId()->getValue(),
            'title' => $song['song']->getTitle(),
            'genre_id' => $song['song']->getGenreId()->getValue(),
            'album_id' => $song['song']->getAlbumId() ? $song['song']->getAlbumId()->getValue() : null,
            'contributions' => $contributions
        ];
    }

    public function createSong(string $title, int $genreId, int $albumId, array $contributions = []) {
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

        // create Song entity
        $song = new Song($title, new GenreId($genreId), new AlbumId($albumId));

        foreach ($contributions as $contributor) {
            $song->addContributor(new ArtistId($contributor['artist_id']), new RoleId($contributor['role_id']));
        }

        // save song to repository
        $this->songRepository->create($song);

        return $this->toArray($song, true);
    }

    public function updateSong(int $id, ?string $title = null, ?int $genreId = null, ?int $albumId = null, ?array $contributions = null): array {
        $song = $this->songRepository->find(new SongId($id));
        if (!$song) {
            throw new \DomainException('Song not found');
        }

        if ($title !== null) {
            $song->setTitle($title);
        }
        if ($genreId !== null) {
            $song->setGenreId(new GenreId($genreId));
        }
        if ($albumId !== null) {
            $song->setAlbumId(new AlbumId($albumId));
        }
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

            // replace all contributors
            $song->replaceContributors($validatedContributions);
        }

        // save updated song to repository
        $this->songRepository->update($song);

        return $this->toArray($song, true);
    }

    public function deleteSong(int $id): void {
        $song = $this->songRepository->find(new SongId($id));
        if (!$song) {
            throw new \DomainException('Song not found');
        }
        $this->songRepository->delete($song);
    }

    private function toArray(Song $song, bool $withContributions = false): array {
        if ($withContributions) {
            return [
                'id' => $song->getId()->getValue(),
                'title' => $song->getTitle(),
                'genre_id' => $song->getGenreId()->getValue(),
                'album_id' => $song->getAlbumId() ? $song->getAlbumId()->getValue() : null,
                'contributions' => array_map(function ($contribution) {
                    return [
                        'artist_id' => $contribution->getArtistId()->getValue(),
                        'role_id' => $contribution->getRoleId()->getValue(),
                    ];
                }, $song->getContributions())
            ];
        }

        return [
            'id' => $song->getId()->getValue(),
            'title' => $song->getTitle(),
            'genre_id' => $song->getGenreId()->getValue(),
            'album_id' => $song->getAlbumId() ? $song->getAlbumId()->getValue() : null,
        ];
        ;
    }
}