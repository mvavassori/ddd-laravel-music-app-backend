<?php

namespace App\Infrastructure\Persistance\Repositories;

use Illuminate\Support\Facades\DB;
use App\Domain\MusicCatalog\Entities\Song;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\MusicCatalog\ValueObjects\GenreId;
use App\Infrastructure\Persistance\Mappers\RoleMapper;
use App\Infrastructure\Persistance\Mappers\SongMapper;
use App\Infrastructure\Persistance\Mappers\AlbumMapper;
use App\Infrastructure\Persistance\Mappers\ArtistMapper;
use App\Infrastructure\Persistance\Models\EloquentSongModel;
use App\Domain\MusicCatalog\Repositories\SongRepositoryInterface;

class EloquentSongRepository implements SongRepositoryInterface {
    private ArtistMapper $artistMapper;
    private AlbumMapper $albumMapper;
    private RoleMapper $roleMapper;
    private SongMapper $songMapper;
    public function __construct(ArtistMapper $artistMapper, AlbumMapper $albumMapper, RoleMapper $roleMapper, SongMapper $songMapper) {
        $this->mapper = $artistMapper;
        $this->albumMapper = $albumMapper;
        $this->roleMapper = $roleMapper;
        $this->songMapper = $songMapper;
    }
    public function find(SongId $id) {
        $eloquentSong = EloquentSongModel::find($id->getValue());
        if (!$eloquentSong) {
            return null;
        }
        return $this->songMapper->toDomain($eloquentSong);
    }

    public function findWithContributions(SongId $id) {
        $eloquentSong = EloquentSongModel::find($id->getValue());
        if (!$eloquentSong) {
            return null;
        }
        $song = $this->songMapper->toDomain($eloquentSong);

        // find contributions
        $contributions = $eloquentSong->contributions()->get();

        $songContributions = [];

        foreach ($contributions as $contribution) {
            $artist = $this->artistMapper->toDomain($contribution->artist);
            $role = $this->roleMapper->toDomain($contribution->role);
            $songContributions[] = [
                'role' => $role,
                'artist' => $artist,
            ];
        }

        return [
            'song' => $song,
            'contributions' => $songContributions
        ];
    }

    public function create(Song $song) {
        $eloquentSong = null;
        DB::transaction(function () use ($song, &$eloquentSong) {
            $eloquentSong = EloquentSongModel::create($this->songMapper->toPersistence($song));
            $contributionsData = [];
            foreach ($song->getContributions() as $contribution) {
                $contributionsData[] = [
                    'artist_id' => $contribution->getArtistId()->getValue(),
                    'role_id' => $contribution->getRoleId()->getValue(),
                    'contributable_type' => EloquentSongModel::class,
                    'contributable_id' => $song->getId()->getValue()
                ];
            }
            if (!empty($contributionsData)) {
                $eloquentSong->contributions()->createMany($contributionsData);
            }
        });
        return $this->songMapper->toDomain($eloquentSong);
    }

    public function update(Song $song) {
        $eloquentSong = EloquentSongModel::find($song->getId()->getValue());
        if (!$eloquentSong) {
            return null;
        }
        DB::transaction(function () use ($song, &$eloquentSong) {
            $eloquentSong->update($this->songMapper->toPersistence($song));

            // $eloquentSong->update([
            //     'title' => $data['title'] ?? $eloquentSong->title,
            //     'image_url' => $data['image_url'] ?? $eloquentSong->image_url,
            //     'genre' => $data['genre'] ?? $eloquentSong->genre,
            //     'description' => $data['description'] ?? $eloquentSong->description
            // ]);

            // delete existing contributors

            $eloquentSong->contributions()->delete();

            $contributionsData = [];
            foreach ($song->getContributions() as $contribution) {
                $contributionsData[] = [
                    'artist_id' => $contribution->getArtistId()->getValue(),
                    'role_id' => $contribution->getRoleId()->getValue(),
                    'contributable_type' => EloquentSongModel::class,
                    'contributable_id' => $eloquentSong->id
                ];
            }

            if (!empty($contributionsData)) {
                $eloquentSong->contributions()->createMany($contributionsData);
            }
        });
    }

    public function delete(SongId $id) {
        EloquentSongModel::destroy($id->getValue());
    }

    public function getSongIdsByGenreAtRandom(GenreId $genreId, $limit = 10) {
        $eloquentSongIds = EloquentSongModel::where('genre_id', $genreId->getValue())
            ->inRandomOrder() // picks songs with specified genre randomly
            ->limit($limit)
            ->pluck('id');
        if(empty($eloquentSongs)) {
            return null; 
        }
        $songIds = [];
        foreach ($eloquentSongIds as $eloquentSongId) {
            $songIds[] = new SongId($eloquentSongId);
        }
        // array of SongId(s)
        return $songIds;
    }
}