<?php

namespace App\Infrastructure\Persistance\Repositories;

use App\Domain\MusicCatalog\ValueObjects\Contribution;
use Illuminate\Support\Facades\DB;
use App\Domain\MusicCatalog\Entities\Album;
use App\Domain\MusicCatalog\ValueObjects\AlbumId;
use App\Infrastructure\Persistance\Mappers\SongMapper;
use App\Infrastructure\Persistance\Mappers\AlbumMapper;
use App\Infrastructure\Persistance\Models\EloquentSongModel;
use App\Infrastructure\Persistance\Models\EloquentAlbumModel;
use App\Domain\MusicCatalog\Repositories\AlbumRepositoryInterface;

class EloquentAlbumRepository implements AlbumRepositoryInterface {

    private AlbumMapper $albumMapper;
    private SongMapper $songMapper;

    public function __construct(AlbumMapper $albumMapper, SongMapper $songMapper) {
        $this->albumMapper = $albumMapper;
        $this->songMapper = $songMapper;
    }

    public function find(AlbumId $id) {
        $eloquentAlbum = EloquentAlbumModel::find($id->getValue());
        if (!$eloquentAlbum) {
            return null;
        }
        return $this->albumMapper->toDomain($eloquentAlbum);
    }

    public function findWithSongs(AlbumId $id) {
        $eloquentAlbum = EloquentAlbumModel::find($id->getValue());
        if (!$eloquentAlbum) {
            return null;
        }
        $album = $this->albumMapper->toDomain($eloquentAlbum);

        // find songs
        $eloquentSongsOfAlbum = EloquentSongModel::where('album_id', $eloquentAlbum->id)->get();

        $songs = [];

        foreach ($eloquentSongsOfAlbum as $song) {
            $songs[] = $this->songMapper->toDomain($song);
        }

        return [
            'album' => $album,
            'songs' => $songs
        ];
    }

    public function create(Album $album) {
        $eloquentAlbum = null;
        DB::transaction(function () use ($album, &$eloquentAlbum) {
            $eloquentAlbum = EloquentAlbumModel::create($this->albumMapper->toPersistence($album));
            $contributionsData = [];
            foreach ($album->getContributions() as $contribution) {
                $contributionsData[] = [
                    'artist_id' => $contribution->getArtistId()->getValue(),
                    'role_id' => $contribution->getRoleId()->getValue(),
                    'contributable_type' => EloquentAlbumModel::class,
                    'contributable_id' => $album->getId()->getValue()
                ];
            }
            if (!empty($contributionsData)) {
                $eloquentAlbum->contributions()->createMany($contributionsData);
            }
        });
        return $this->albumMapper->toDomain($eloquentAlbum);
    }

    public function update(Album $album) {
        $eloquentAlbum = EloquentAlbumModel::find($album->getId()->getValue());
        if (!$eloquentAlbum) {
            return null;
        }
        DB::transaction(function () use ($album, &$eloquentAlbum) {
            $eloquentAlbum->update($this->albumMapper->toPersistence($album));

            // $eloquentAlbum->update([
            //     'title' => $data['title'] ?? $eloquentAlbum->title,
            //     'image_url' => $data['image_url'] ?? $eloquentAlbum->image_url,
            //     'genre' => $data['genre'] ?? $eloquentAlbum->genre,
            //     'description' => $data['description'] ?? $eloquentAlbum->description
            // ]);

            // delete existing contributors
            $eloquentAlbum->contributions()->delete();

            $contributionsData = [];
            foreach ($album->getContributions() as $contribution) {
                $contributionsData[] = [
                    'artist_id' => $contribution->getArtistId()->getValue(),
                    'role_id' => $contribution->getRoleId()->getValue(),
                    'contributable_type' => EloquentAlbumModel::class,
                    'contributable_id' => $eloquentAlbum->id
                ];
            }

            if (!empty($contributionsData)) {
                $eloquentAlbum->contributions()->createMany($contributionsData);
            }
        });

        // return $eloquentAlbum->load(['contributions.artist', 'contributions.role']);
        // return $this->albumMapper->toDomain($eloquentAlbum);
    }

    public function delete(AlbumId $id) {
        EloquentAlbumModel::destroy($id->getValue());
    }
}