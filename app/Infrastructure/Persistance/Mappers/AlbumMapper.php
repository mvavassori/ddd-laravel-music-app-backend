<?php

namespace App\Infrastructure\Persistance\Mappers;

use App\Domain\MusicCatalog\Entities\Album;
use App\Domain\MusicCatalog\ValueObjects\AlbumId;
use App\Domain\MusicCatalog\ValueObjects\AlbumImageUrl;
use App\Infrastructure\Persistance\Models\EloquentAlbumModel;


class AlbumMapper {
    public function toDomain(EloquentAlbumModel $eloquentAlbum): Album {
        $imageUrl = null;
        if ($eloquentAlbum->image_url) {
            $imageUrl = new AlbumImageUrl($eloquentAlbum->image_url);
        }

        $id = new AlbumId($eloquentAlbum->id);

        $album = Album::fromPersistance(
            $id,
            $eloquentAlbum->title,
            $eloquentAlbum->description,
            $imageUrl
        );
        return $album;
    }

    public function toPersistence(Album $album) {
        return [
            'id' => $album->getId()->getValue(),
            'title' => $album->getTitle(),
            'description' => $album->getDescription(),
            'image_url' => $album->getImageUrl()?->getValue(),
        ];
    }
}