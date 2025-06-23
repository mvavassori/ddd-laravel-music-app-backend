<?php

namespace App\Infrastructure\Persistance\Repositories;

use Illuminate\Support\Facades\DB;
use App\Domain\MusicCatalog\Entities\Artist;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Infrastructure\Persistance\Mappers\RoleMapper;
use App\Infrastructure\Persistance\Mappers\SongMapper;
use App\Infrastructure\Persistance\Mappers\AlbumMapper;
use App\Infrastructure\Persistance\Mappers\ArtistMapper;
use App\Infrastructure\Persistance\Models\EloquentSongModel;
use App\Infrastructure\Persistance\Models\EloquentAlbumModel;
use App\Infrastructure\Persistance\Models\EloquentArtistModel;
use App\Domain\MusicCatalog\Repositories\ArtistRepositoryInterface;
use App\Infrastructure\Persistance\Models\EloquentContributionModel;

// actual implementation of ArtistRepositoryInterface

class EloquentArtistRepository implements ArtistRepositoryInterface {
    private ArtistMapper $artistMapper;
    private AlbumMapper $albumMapper;
    private RoleMapper $roleMapper;
    private SongMapper $songMapper;
    public function __construct(ArtistMapper $artistMapper, AlbumMapper $albumMapper, RoleMapper $roleMapper, SongMapper $songMapper) {
        $this->artistMapper = $artistMapper;
        $this->albumMapper = $albumMapper;
        $this->roleMapper = $roleMapper;
        $this->songMapper = $songMapper;
    }

    public function all() {
        return EloquentArtistModel::all();
    }

    public function find(ArtistId $id): Artist|null {
        $eloquentArtist = EloquentArtistModel::find($id->getValue());
        if (!$eloquentArtist) {
            return null;
        }
        return $this->artistMapper->toDomain($eloquentArtist);
    }

    public function findByName($name): Artist|null {
        $eloquentArtist = EloquentArtistModel::where(column: 'name', value: $name)->first();
        if (!$eloquentArtist) {
            return null;
        }
        return $this->artistMapper->toDomain($eloquentArtist);
    }

    public function create(Artist $artist): Artist {
        DB::transaction(function () use (&$artist) {
            $artist = EloquentArtistModel::create($this->artistMapper->toPersistence($artist));
        });
        return $this->artistMapper->toDomain($artist);
    }

    public function update(Artist $artist) {
        DB::transaction(function () use ($artist) {
            EloquentArtistModel::where('id', $artist->getId()->getValue())
                ->update($this->artistMapper->toPersistence($artist));
        });
    }

    public function delete(ArtistId $id) {
        return EloquentArtistModel::destroy($id->getValue());
    }

    public function findWithContributions(ArtistId $id) {
        $eloquentArtist = EloquentArtistModel::find($id->getValue());

        if (!$eloquentArtist) {
            return null;
        }

        $artist = $this->artistMapper->toDomain($eloquentArtist);

        $contributions = EloquentContributionModel::where('artist_id', $id)
            ->with(['role', 'contributable'])
            ->get();


        $albumContributions = [];
        $songContributions = [];

        foreach ($contributions as $contribution) {
            $role = $this->roleMapper->toDomain($contribution->role);

            if ($contribution->contributable_type === EloquentAlbumModel::class) {
                $album = $this->albumMapper->toDomain($contribution->contributable);
                $albumContributions[] = [
                    'album' => $album,
                    'role' => $role
                ];
            } elseif ($contribution->contributable_type === EloquentSongModel::class) {
                $song = $this->songMapper->toDomain($contribution->contributable);
                $songContributions[] = [
                    'song' => $song,
                    'role' => $role
                ];
            }
        }

        return [
            'artist' => $artist,
            'albumContributions' => $albumContributions,
            'songContributions' => $songContributions
        ];
    }

    public function findWithSongs($id) {
        $eloquentArtist = EloquentArtistModel::find($id);

        if (!$eloquentArtist) {
            return null;
        }

        $artist = $this->artistMapper->toDomain($eloquentArtist);

        $eloquentSongContributions = EloquentContributionModel::where('artist_id', $id)->where('contributable_type', EloquentSongModel::class)
            ->with(['role', 'contributable'])
            ->get();

        $songContributions = [];

        foreach ($eloquentSongContributions as $songContribution) {
            $song = $this->songMapper->toDomain($songContribution->contributable);
            $role = $this->roleMapper->toDomain($songContribution->role);

            $songContributions[] = [
                'song' => $song,
                'role' => $role
            ];
        }

        return [
            'artist' => $artist,
            'songContributions' => $songContributions
        ];
    }

    public function findWithAlbums($id) {
        $eloquentArtist = EloquentArtistModel::find($id);

        if (!$eloquentArtist) {
            return null;
        }

        $artist = $this->artistMapper->toDomain($eloquentArtist);

        $eloquentAlbumContributions = EloquentContributionModel::where('artist_id', $id)->where('contributable_type', EloquentAlbumModel::class)
            ->with(['role', 'contributable'])
            ->get();

        $albumContributions = [];

        foreach ($eloquentAlbumContributions as $albumContribution) {
            $album = $this->albumMapper->toDomain($albumContribution->contributable);
            $role = $this->roleMapper->toDomain($albumContribution->role);

            $albumContributions[] = [
                'album' => $album,
                'role' => $role
            ];
        }

        return [
            'artist' => $artist,
            'albumContributions' => $albumContributions
        ];
    }
}